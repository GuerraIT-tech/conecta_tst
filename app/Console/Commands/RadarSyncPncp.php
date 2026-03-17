<?php

namespace App\Console\Commands;

use App\Models\RadarPreference;
use App\Models\RadarResult;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class RadarSyncPncp extends Command
{
    protected $signature = 'radar:sync-pncp {--user=} {--debug}';
    protected $description = 'Sincroniza diariamente editais do PNCP conforme preferências dos usuários';

    public const ENDPOINT_PREGOS = 'https://dadosabertos.compras.gov.br/modulo-contratacoes/1_consultarContratacoes_PNCP_14133';

    public const REGION_UFS = [
        'sul' => ['PR','SC','RS'],
        'sudeste' => ['SP','RJ','MG','ES'],
        'centro-oeste' => ['DF','GO','MT','MS'],
        'nordeste' => ['AL','BA','CE','MA','PB','PE','PI','RN','SE'],
        'norte' => ['AC','AP','AM','PA','RO','RR','TO'],
    ];

    public function handle(): int
    {
        $q = RadarPreference::query();

        if ($this->option('user')) {
            $q->where('user_id', (int) $this->option('user'));
        }

        $prefs = $q->get();

        foreach ($prefs as $pref) {
            $this->syncForPreference($pref, (bool) $this->option('debug'));
        }

        $this->info('OK');
        return self::SUCCESS;
    }

    protected function syncForPreference(RadarPreference $pref, bool $debug = false): void
    {
        $userId = (int) $pref->user_id;

        $from = $pref->last_synced_at
            ? $pref->last_synced_at->copy()->subDay()->format('Y-m-d')
            : now()->subDay()->format('Y-m-d');

        $to = now()->format('Y-m-d');

        $keywordRaw = (string) ($pref->keyword ?? '');
        $regionsRaw = $this->forceArray($pref->regions ?? []);
        $ufsRaw     = $this->forceArray($pref->ufs ?? []);

        $keywords   = $this->parseKeywords($keywordRaw); // vazio => pega tudo
        $allowedUfs = $this->computeAllowedUfs($regionsRaw, $ufsRaw);

        $items = $this->fetchPregoes($from, $to);

        $excludedKeyword = 0;
        $excludedUf = 0;
        $saved = 0;

        if ($debug) {
            $this->line("---- DEBUG user_id={$userId} ----");
            $this->line("keyword='{$keywordRaw}' | keywords=" . json_encode($keywords, JSON_UNESCAPED_UNICODE));
            $this->line("regions=" . json_encode($regionsRaw, JSON_UNESCAPED_UNICODE));
            $this->line("ufs=" . json_encode($ufsRaw, JSON_UNESCAPED_UNICODE));
            $this->line("allowedUfs=" . json_encode($allowedUfs, JSON_UNESCAPED_UNICODE));
            $this->line("intervalo: {$from} -> {$to} | retornados=" . count($items));
        }

        foreach ($items as $p) {
            if (!is_array($p)) continue;

            $obj = (string) ($p['objetoCompra'] ?? '');
            $uf  = strtoupper(trim((string) ($p['unidadeOrgaoUfSigla'] ?? '')));
            $idCompra = trim((string) ($p['idCompra'] ?? ''));

            if ($idCompra === '') continue;

            if (!$this->matchKeywords($obj, $keywords)) {
                $excludedKeyword++;
                continue;
            }

            if (!empty($allowedUfs) && ($uf === '' || !in_array($uf, $allowedUfs, true))) {
                $excludedUf++;
                continue;
            }

            RadarResult::updateOrCreate(
                ['user_id' => $userId, 'id_compra' => $idCompra],
                [
                    'numero_controle_pncp' => $p['numeroControlePNCP'] ?? null,
                    'orgao' => $p['orgaoEntidadeRazaoSocial'] ?? null,
                    'uf' => $uf ?: null,
                    'municipio' => $p['unidadeOrgaoMunicipioNome'] ?? null,
                    'modalidade' => $p['modalidadeNome'] ?? null,
                    'data_publicacao' => $this->toDateTime($p['dataPublicacaoPncp'] ?? null),
                    'data_encerramento' => $this->toDateTime($p['dataEncerramentoPropostaPncp'] ?? null),
                    'objeto' => $obj ?: null,
                    'payload' => $p,
                ]
            );

            $saved++;
        }

        $pref->last_synced_at = now();
        $pref->save();

        $this->line("user_id={$userId} | retornados=" . count($items) . " | excl_keyword={$excludedKeyword} | excl_uf={$excludedUf} | salvos={$saved}");
    }

    protected function forceArray($value): array
    {
        if (is_array($value)) return $value;

        if (is_string($value)) {
            $v = trim($value);

            if ($v !== '' && ($v[0] === '[' || $v[0] === '{')) {
                $decoded = json_decode($v, true);
                if (is_array($decoded)) return $decoded;
            }

            if (str_contains($v, ',')) {
                return array_values(array_filter(array_map('trim', explode(',', $v))));
            }

            return $v === '' ? [] : [$v];
        }

        return [];
    }

    protected function fetchPregoes(string $dataInicial, string $dataFinal): array
    {
        try {
            $resp = Http::timeout(20)->acceptJson()->retry(2, 250)->get(self::ENDPOINT_PREGOS, [
                'pagina' => 1,
                'tamanhoPagina' => 500,
                'dataPublicacaoPncpInicial' => $dataInicial,
                'dataPublicacaoPncpFinal' => $dataFinal,
                'codigoModalidade' => '05',
            ]);

            if ($resp->failed()) return [];

            $json = $resp->json();
            return (isset($json['resultado']) && is_array($json['resultado'])) ? $json['resultado'] : [];
        } catch (\Throwable) {
            return [];
        }
    }

    // keyword vazia => SEM FILTRO (pega tudo)
    protected function parseKeywords(string $keyword): array
    {
        $keyword = trim($keyword);
        if ($keyword === '') return [];

        $keyword = $this->normalize($keyword);

        $parts = preg_split('~[,\-;]+~', $keyword) ?: [];
        $parts = array_values(array_filter(array_map(fn ($s) => trim($s), $parts)));

        // singular/plural simples (equipamentos -> equipamento)
        $expanded = [];
        foreach ($parts as $p) {
            $expanded[$p] = true;
            if (mb_strlen($p) > 3 && str_ends_with($p, 's')) {
                $expanded[rtrim($p, 's')] = true;
            }
        }

        return array_keys($expanded);
    }

    protected function matchKeywords(string $text, array $keywords): bool
    {
        if (empty($keywords)) return true; // sem keyword => pega tudo

        $text = $this->normalize($text);

        foreach ($keywords as $k) {
            if ($k !== '' && str_contains($text, $k)) return true;
        }

        return false;
    }

    protected function normalize(string $s): string
    {
        $s = mb_strtolower($s);

        $converted = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s);
        if (is_string($converted)) {
            $s = $converted;
        }

        $s = preg_replace('/[^a-z0-9\s]/', ' ', $s) ?? $s;
        $s = preg_replace('/\s+/', ' ', trim($s)) ?? trim($s);

        return $s;
    }

    protected function computeAllowedUfs(array $regions, array $ufs): array
    {
        $regions = array_values(array_filter(array_map(function ($r) {
            $r = mb_strtolower(trim((string) $r));
            $r = str_replace(['_', ' '], ['-', '-'], $r);
            return $r;
        }, $regions)));

        $ufs = array_values(array_filter(array_map(fn ($u) => strtoupper(trim((string) $u)), $ufs)));

        $regionUfs = [];
        foreach ($regions as $r) {
            foreach (self::REGION_UFS[$r] ?? [] as $uf) {
                $regionUfs[$uf] = true;
            }
        }
        $regionUfs = array_keys($regionUfs);

        if (!empty($ufs) && !empty($regionUfs)) {
            return array_values(array_intersect($ufs, $regionUfs));
        }

        if (!empty($ufs)) return $ufs;
        if (!empty($regionUfs)) return $regionUfs;

        return [];
    }

    protected function toDateTime(?string $value): ?string
    {
        if (!$value) return null;
        try {
            return \Carbon\Carbon::parse($value)->toDateTimeString();
        } catch (\Throwable) {
            return null;
        }
    }
}
