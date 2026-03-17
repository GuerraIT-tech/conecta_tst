<?php

namespace App\Filament\Pages;

use App\Models\RadarPreference;
use App\Models\RadarResult;
use App\Models\SavedPregao;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class RadarV2 extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Inteligência';
    protected static ?string $navigationLabel = 'Radar V2';
    protected static ?string $slug = 'radar-v2';

    protected static string $view = 'filament.pages.radar-v2';

    // ===== Estado =====
    public bool $loaded = false;
    public ?string $errorMessage = null;

    /** Lista base exibida (agora vem de RadarResult) */
    public array $allPregoes = [];
    public array $savedIds = []; // [idCompra, idCompra...]

    public array $stats = [
        'abertos' => 0,
        'fechando' => 0,
        'fechado' => 0,
    ];

    // Tabs: abertos | fechando | fechado | favoritos
    public string $activeTab = 'abertos';

    // ===== Filtros da TELA (adicionais) =====
    public string $q = '';
    public ?string $fUf = null;
    public ?string $fModalidade = null;
    public ?string $fSrp = null;
    public ?float $fValorMin = null;
    public ?float $fValorMax = null;
    public ?string $fEncFrom = null;
    public ?string $fEncTo = null;

    // ===== Preferências (tela anterior) =====
    public ?string $prefKeyword = null;
    public array $prefRegions = [];
    public array $prefUfs = [];
    public array $prefAllowedUfs = []; // resultado (região + ufs)
    public bool $hasPreference = false;

    // ===== Modal =====
    public string $modalTab = 'geral';

    public array $selectedPregao = [];
    public array $selectedItens = [];
    public array $selectedDocumentos = [];

    public ?string $editalUrl = null;
    public ?string $editalDownloadUrl = null;
    public ?string $pregaoUrl = null;

    // ===== Endpoints (itens / docs) =====
    public const ENDPOINT_ITENS = 'https://dadosabertos.compras.gov.br/modulo-contratacoes/2_consultarItensContratacoes_PNCP_14133';
    public const ENDPOINT_DOCUMENTOS = 'https://dadosabertos.compras.gov.br/modulo-contratacoes/3_consultarArquivosContratacoes_PNCP_14133';

    public const REGION_UFS = [
        'Sul' => ['PR','SC','RS'],
        'Sudeste' => ['SP','RJ','MG','ES'],
        'Centro-Oeste' => ['DF','GO','MT','MS'],
        'Nordeste' => ['AL','BA','CE','MA','PB','PE','PI','RN','SE'],
        'Norte' => ['AC','AP','AM','PA','RO','RR','TO'],
    ];

    public function mount(): void
    {
        $this->refreshSavedIds();
        $this->loadPreferenceForUser();
    }

    protected function loadPreferenceForUser(): void
    {
        if (!auth()->check()) {
            $this->hasPreference = false;
            return;
        }

        $pref = RadarPreference::query()
            ->where('user_id', auth()->id())
            ->first();

        if (!$pref) {
            $this->hasPreference = false;
            return;
        }

        $this->hasPreference = true;

        $this->prefKeyword = trim((string) ($pref->keyword ?? ''));
        $this->prefRegions = is_array($pref->regions) ? $pref->regions : [];
        $this->prefUfs     = is_array($pref->ufs) ? $pref->ufs : [];

        $this->prefAllowedUfs = $this->computeAllowedUfs($this->prefRegions, $this->prefUfs);

        // Se só existir 1 UF, já trava visualmente
        if (count($this->prefAllowedUfs) === 1) {
            $this->fUf = $this->prefAllowedUfs[0];
        } else {
            // se o filtro atual está fora do permitido, zera
            if ($this->fUf && !in_array($this->fUf, $this->prefAllowedUfs, true)) {
                $this->fUf = null;
            }
        }
    }

    // =========================================================
    // Computed
    // =========================================================
    public function getFilteredPregoesProperty(): array
    {
        // Fonte dos dados: Favoritos ou Resultados do Radar
        $list = $this->activeTab === 'favoritos'
            ? $this->getSavedAsArray()
            : $this->allPregoes;

        // 0) aplica SEMPRE as preferências (hard filter)
        $list = $this->applyPreferenceFilters($list);

        // 1) filtro por status (se NÃO for favoritos)
        if ($this->activeTab !== 'favoritos') {
            $list = array_values(array_filter($list, fn ($p) => ($p['status'] ?? 'abertos') === $this->activeTab));
        }

        // 2) aplica filtros adicionais de tela
        $q = trim(mb_strtolower($this->q));

        $list = array_values(array_filter($list, function ($p) use ($q) {

            // busca geral
            if ($q !== '') {
                $hay = mb_strtolower(
                    ($p['numeroControlePNCP'] ?? '') . ' ' .
                    ($p['orgaoEntidadeRazaoSocial'] ?? '') . ' ' .
                    ($p['objetoCompra'] ?? '') . ' ' .
                    ($p['processo'] ?? '') . ' ' .
                    ($p['idCompra'] ?? '')
                );

                if (!str_contains($hay, $q)) {
                    return false;
                }
            }

            // UF (mas só dentro do permitido)
            if (!empty($this->fUf)) {
                $uf = $p['unidadeOrgaoUfSigla'] ?? $p['uf'] ?? null;
                if (($uf ?? '') !== $this->fUf) return false;
            }

            // Modalidade
            if (!empty($this->fModalidade)) {
                $mod = $p['modalidadeNome'] ?? $p['modalidade'] ?? null;
                if (($mod ?? '') !== $this->fModalidade) return false;
            }

            // SRP
            if ($this->fSrp !== null && $this->fSrp !== '') {
                $srp = !empty($p['srp']) ? true : false;
                if ($this->fSrp === '1' && !$srp) return false;
                if ($this->fSrp === '0' && $srp) return false;
            }

            // Valor min/max
            $valor = (float) ($p['valorTotalEstimado'] ?? $p['valor_estimado'] ?? 0);

            if ($this->fValorMin !== null && $valor < $this->fValorMin) return false;
            if ($this->fValorMax !== null && $valor > $this->fValorMax) return false;

            // Encerramento de/até
            $enc = $p['dataEncerramentoPropostaPncp'] ?? $p['data_encerramento'] ?? null;
            if (!empty($enc)) {
                try {
                    $encDate = \Carbon\Carbon::parse($enc)->toDateString();

                    if (!empty($this->fEncFrom) && $encDate < $this->fEncFrom) return false;
                    if (!empty($this->fEncTo) && $encDate > $this->fEncTo) return false;
                } catch (\Throwable) {
                    // ignora filtro por data se não parsear
                }
            }

            return true;
        }));

        // 3) marca "saved"
        foreach ($list as &$p) {
            $id = $p['idCompra'] ?? $p['id_compra'] ?? null;
            $p['_saved'] = $id ? in_array($id, $this->savedIds, true) : false;
        }

        return $list;
    }

    // =========================================================
    // Tabs / filtros
    // =========================================================
    public function setTab(string $tab): void
    {
        if (!in_array($tab, ['abertos', 'fechando', 'fechado', 'favoritos'], true)) return;
        $this->activeTab = $tab;
    }

    public function clearFilters(): void
    {
        $this->q = '';
        // UF: se tiver 1 permitido, mantém travado
        if (count($this->prefAllowedUfs) === 1) {
            $this->fUf = $this->prefAllowedUfs[0];
        } else {
            $this->fUf = null;
        }

        $this->fModalidade = null;
        $this->fSrp = null;
        $this->fValorMin = null;
        $this->fValorMax = null;
        $this->fEncFrom = null;
        $this->fEncTo = null;
    }

    // =========================================================
    // Load pregões (AGORA: do banco RadarResult)
    // =========================================================
    public function loadPregoes(): void
    {
        if ($this->loaded) return;
        $this->loaded = true;

        $this->errorMessage = null;

        if (!auth()->check()) {
            $this->errorMessage = 'Faça login para acessar o Radar.';
            return;
        }

        // Se não tem preferência, essa página deve existir, mas o ideal é redirecionar
        if (!$this->hasPreference) {
            $this->allPregoes = [];
            $this->stats = ['abertos' => 0, 'fechando' => 0, 'fechado' => 0];
            $this->errorMessage = 'Você ainda não configurou seu Radar. Clique em "Alterar filtros" para configurar.';
            return;
        }

        try {
            // carrega resultados do radar para o usuário
            $rows = RadarResult::query()
                ->where('user_id', auth()->id())
                ->orderByDesc('data_publicacao')
                ->limit(2500)
                ->get();

            $list = [];

            foreach ($rows as $r) {
                $payload = is_array($r->payload ?? null) ? $r->payload : [];

                // monta no formato que o blade já espera
                $p = $payload;

                $p['idCompra'] = (string) ($r->id_compra ?? ($payload['idCompra'] ?? ''));
                $p['numeroControlePNCP'] = $payload['numeroControlePNCP'] ?? $r->numero_controle_pncp ?? null;

                $p['orgaoEntidadeRazaoSocial'] = $payload['orgaoEntidadeRazaoSocial'] ?? $r->orgao ?? null;
                $p['unidadeOrgaoUfSigla'] = $payload['unidadeOrgaoUfSigla'] ?? $r->uf ?? null;
                $p['unidadeOrgaoMunicipioNome'] = $payload['unidadeOrgaoMunicipioNome'] ?? $r->municipio ?? null;

                $p['modalidadeNome'] = $payload['modalidadeNome'] ?? $r->modalidade ?? null;

                $p['dataPublicacaoPncp'] = $payload['dataPublicacaoPncp'] ?? optional($r->data_publicacao)->toIso8601String();
                $p['dataEncerramentoPropostaPncp'] = $payload['dataEncerramentoPropostaPncp'] ?? optional($r->data_encerramento)->toIso8601String();

                $p['objetoCompra'] = $payload['objetoCompra'] ?? $r->objeto ?? null;

                $status = $this->classifyPregao($p);
                $p['status'] = $status;
                $p['statusLabel'] = $this->getStatusLabel($status);

                $list[] = $p;
            }

            // aplica preferências como “hard filter” também aqui (por garantia)
            $list = $this->applyPreferenceFilters($list);

            $this->allPregoes = $list;

            // stats
            $stats = ['abertos' => 0, 'fechando' => 0, 'fechado' => 0];
            foreach ($this->allPregoes as $p) {
                $st = $p['status'] ?? 'abertos';
                $stats[$st] = ($stats[$st] ?? 0) + 1;
            }
            $this->stats = $stats;

            $this->refreshSavedIds();
        } catch (\Throwable $e) {
            $this->errorMessage = 'Erro ao carregar o Radar: ' . $e->getMessage();
            $this->allPregoes = [];
        }
    }

    // =========================================================
    // Favoritar / desfavoritar
    // =========================================================
    public function toggleSave(string $idCompra): void
    {
        if (blank($idCompra) || !auth()->check()) return;

        $userId = auth()->id();

        $exists = SavedPregao::query()
            ->where('user_id', $userId)
            ->where('id_compra', $idCompra)
            ->first();

        if ($exists) {
            $exists->delete();
            $this->refreshSavedIds();

            if (($this->selectedPregao['idCompra'] ?? null) === $idCompra) {
                $this->selectedPregao['_saved'] = false;
            }
            return;
        }

        // acha o pregão na lista atual
        $pregao = null;
        foreach ($this->allPregoes as $p) {
            if (($p['idCompra'] ?? null) === $idCompra) {
                $pregao = $p;
                break;
            }
        }

        $data = $pregao ?? ['idCompra' => $idCompra];

        SavedPregao::create([
            'user_id' => $userId,
            'id_compra' => $idCompra,
            'numero_controle_pncp' => $data['numeroControlePNCP'] ?? null,
            'orgao' => $data['orgaoEntidadeRazaoSocial'] ?? null,
            'uf' => $data['unidadeOrgaoUfSigla'] ?? null,
            'municipio' => $data['unidadeOrgaoMunicipioNome'] ?? null,
            'modalidade' => $data['modalidadeNome'] ?? null,
            'modo_disputa' => $data['modoDisputaNomePncp'] ?? null,
            'processo' => $data['processo'] ?? null,
            'srp' => !empty($data['srp']),
            'valor_estimado' => isset($data['valorTotalEstimado']) ? (float) $data['valorTotalEstimado'] : null,
            'data_publicacao' => !empty($data['dataPublicacaoPncp']) ? $data['dataPublicacaoPncp'] : null,
            'data_abertura' => !empty($data['dataAberturaPropostaPncp']) ? $data['dataAberturaPropostaPncp'] : null,
            'data_encerramento' => !empty($data['dataEncerramentoPropostaPncp']) ? $data['dataEncerramentoPropostaPncp'] : null,
            'objeto' => $data['objetoCompra'] ?? null,
            'payload' => $pregao ? $pregao : null,
        ]);

        $this->refreshSavedIds();

        if (($this->selectedPregao['idCompra'] ?? null) === $idCompra) {
            $this->selectedPregao['_saved'] = true;
        }
    }

    protected function refreshSavedIds(): void
    {
        if (!auth()->check()) {
            $this->savedIds = [];
            return;
        }

        $this->savedIds = SavedPregao::query()
            ->where('user_id', auth()->id())
            ->pluck('id_compra')
            ->all();
    }

    protected function getSavedAsArray(): array
    {
        if (!auth()->check()) return [];

        return SavedPregao::query()
            ->where('user_id', auth()->id())
            ->latest()
            ->get()
            ->map(function (SavedPregao $s) {
                $p = [
                    'idCompra' => $s->id_compra,
                    'numeroControlePNCP' => $s->numero_controle_pncp,
                    'orgaoEntidadeRazaoSocial' => $s->orgao,
                    'unidadeOrgaoUfSigla' => $s->uf,
                    'unidadeOrgaoMunicipioNome' => $s->municipio,
                    'modalidadeNome' => $s->modalidade,
                    'modoDisputaNomePncp' => $s->modo_disputa,
                    'processo' => $s->processo,
                    'srp' => $s->srp,
                    'valorTotalEstimado' => $s->valor_estimado,
                    'dataPublicacaoPncp' => optional($s->data_publicacao)->toIso8601String(),
                    'dataAberturaPropostaPncp' => optional($s->data_abertura)->toIso8601String(),
                    'dataEncerramentoPropostaPncp' => optional($s->data_encerramento)->toIso8601String(),
                    'objetoCompra' => $s->objeto,
                ];

                $status = $this->classifyPregao($p);
                $p['status'] = $status;
                $p['statusLabel'] = $this->getStatusLabel($status);

                return $p;
            })
            ->toArray();
    }

    // =========================================================
    // Modal
    // =========================================================
    public function openDetails(?string $idCompra): void
    {
        if (blank($idCompra)) return;

        $this->modalTab = 'geral';
        $this->selectedPregao = [];
        $this->selectedItens = [];
        $this->selectedDocumentos = [];
        $this->editalUrl = null;
        $this->editalDownloadUrl = null;
        $this->pregaoUrl = null;

        $pregao = null;

        foreach ($this->allPregoes as $p) {
            if (($p['idCompra'] ?? null) === $idCompra) {
                $pregao = $p;
                break;
            }
        }

        if (!$pregao) {
            foreach ($this->getSavedAsArray() as $p) {
                if (($p['idCompra'] ?? null) === $idCompra) {
                    $pregao = $p;
                    break;
                }
            }
        }

        if (!$pregao) {
            $this->errorMessage = 'Pregão não encontrado.';
            return;
        }

        $pregao['_saved'] = in_array($idCompra, $this->savedIds, true);

        $this->selectedPregao = $pregao;

        // links
        $this->editalUrl = $this->buildEditalUrl($pregao);
        $this->pregaoUrl = $this->buildContratosUrl($pregao);

        // cache local
        $this->selectedItens = Cache::store('file')->remember(
            'pncp:prego:itens:' . $idCompra,
            now()->addMinutes(15),
            fn () => $this->fetchItens($idCompra)
        );

        $this->selectedDocumentos = Cache::store('file')->remember(
            'pncp:prego:docs:' . $idCompra,
            now()->addMinutes(15),
            fn () => $this->fetchDocumentos($idCompra)
        );

        // download do edital (se existir)
        $this->editalDownloadUrl = $this->buildEditalDownloadUrl($pregao, $this->selectedDocumentos);

        $this->dispatch('open-modal', id: 'radarV2Modal');
    }

    public function closeModal(): void
    {
        $this->dispatch('close-modal', id: 'radarV2Modal');
    }

    // =========================================================
    // Itens / Documentos
    // =========================================================
    protected function fetchItens(string $idCompra): array
    {
        $json = $this->httpGetJson(self::ENDPOINT_ITENS, [
            'idCompra' => $idCompra,
            'pagina' => 1,
            'tamanhoPagina' => 500,
        ]);

        if (isset($json['resultado']) && is_array($json['resultado'])) return $json['resultado'];
        if (isset($json[0]) && is_array($json[0])) return $json;

        return [];
    }

    protected function fetchDocumentos(string $idCompra): array
    {
        $json = $this->httpGetJson(self::ENDPOINT_DOCUMENTOS, [
            'idCompra' => $idCompra,
            'pagina' => 1,
            'tamanhoPagina' => 200,
        ]);

        $docs = [];
        if (isset($json['resultado']) && is_array($json['resultado'])) $docs = $json['resultado'];
        elseif (isset($json[0]) && is_array($json[0])) $docs = $json;

        return array_values(array_map(function ($d) {
            if (!is_array($d)) return [];

            $url = $d['download_url'] ?? null;
            foreach (['url', 'link', 'urlArquivo', 'urlDownload', 'uri', 'arquivoUrl'] as $k) {
                if (blank($url) && !empty($d[$k]) && is_string($d[$k])) $url = $d[$k];
            }

            $d['download_url'] = $url;
            return $d;
        }, $docs));
    }

    protected function httpGetJson(string $url, array $query): array
    {
        try {
            $resp = Http::timeout(12)->acceptJson()->retry(2, 250)->get($url, $query);
            if ($resp->failed()) return [];
            $json = $resp->json();
            return is_array($json) ? $json : [];
        } catch (\Throwable) {
            return [];
        }
    }

    // =========================================================
    // Status
    // =========================================================
    protected function classifyPregao(array $pregao): string
    {
        $encStr = $pregao['dataEncerramentoPropostaPncp'] ?? $pregao['data_encerramento'] ?? null;

        if (blank($encStr)) return 'fechado';

        try {
            $enc = \Carbon\Carbon::parse($encStr);
            $now = now();

            if ($enc->lt($now)) return 'fechado';

            $days = $now->diffInDays($enc, false);
            if ($days <= 3) return 'fechando';

            return 'abertos';
        } catch (\Throwable) {
            return 'fechado';
        }
    }

    protected function getStatusLabel(string $status): string
    {
        return match ($status) {
            'abertos' => 'ABERTO',
            'fechando' => 'FECHANDO',
            'fechado' => 'FECHADO',
            default => 'STATUS',
        };
    }

    // =========================================================
    // Preferências (hard filter)
    // =========================================================
    protected function applyPreferenceFilters(array $list): array
    {
        // 1) keyword (se vazio => pega tudo)
        $kw = trim((string) ($this->prefKeyword ?? ''));
        $keywords = $this->parseKeywords($kw); // vazio => []

        // 2) UF permitido (região + ufs)
        $allowedUfs = $this->prefAllowedUfs ?? [];

        return array_values(array_filter($list, function ($p) use ($keywords, $allowedUfs) {
            $obj = (string) ($p['objetoCompra'] ?? '');
            $uf  = strtoupper(trim((string) ($p['unidadeOrgaoUfSigla'] ?? $p['uf'] ?? '')));

            if (!$this->matchKeywords($obj, $keywords)) return false;

            if (!empty($allowedUfs) && ($uf === '' || !in_array($uf, $allowedUfs, true))) return false;

            return true;
        }));
    }

    protected function parseKeywords(string $keyword): array
    {
        $keyword = trim($keyword);
        if ($keyword === '') return []; // vazio => pega tudo

        $keyword = $this->normalize($keyword);

        $parts = preg_split('~[,\-;]+~', $keyword) ?: [];
        $parts = array_values(array_filter(array_map(fn ($s) => trim($s), $parts)));

        $expanded = [];
        foreach ($parts as $p) {
            $expanded[$p] = true;
            // singular simples
            if (mb_strlen($p) > 3 && str_ends_with($p, 's')) {
                $expanded[rtrim($p, 's')] = true;
            }
        }

        return array_keys($expanded);
    }

    protected function matchKeywords(string $text, array $keywords): bool
    {
        if (empty($keywords)) return true; // sem keyword => tudo

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
        if (is_string($converted)) $s = $converted;

        $s = preg_replace('/[^a-z0-9\s]/', ' ', $s) ?? $s;
        $s = preg_replace('/\s+/', ' ', trim($s)) ?? trim($s);

        return $s;
    }

    protected function computeAllowedUfs(array $regions, array $ufs): array
    {
        $regions = array_values(array_filter($regions));
        $ufs = array_values(array_filter($ufs));

        $regionUfs = [];
        foreach ($regions as $r) {
            foreach (self::REGION_UFS[$r] ?? [] as $uf) {
                $regionUfs[$uf] = true;
            }
        }
        $regionUfs = array_keys($regionUfs);

        // regra:
        // - se marcou UFs e regiões: interseção
        // - se marcou só UFs: usa UFs
        // - se marcou só regiões: usa regiões
        if (!empty($ufs) && !empty($regionUfs)) {
            return array_values(array_intersect($ufs, $regionUfs));
        }

        if (!empty($ufs)) return $ufs;
        if (!empty($regionUfs)) return $regionUfs;

        return []; // vazio = todas
    }

    // =========================================================
    // Links PNCP (Edital / Contratos / Download)
    // =========================================================
    protected function parseNumeroControle(?string $numeroControle): ?array
    {
        if (!$numeroControle) return null;

        $decoded = urldecode(trim($numeroControle));

        // formato: 45273916000153-1-000001/2026
        if (preg_match('~^(\d{14})-(\d+)-(\d+)[/](\d{4})$~', $decoded, $m)) {
            return [
                'cnpj' => $m[1],
                'unidade' => $m[2],
                'seq' => str_pad($m[3], 6, '0', STR_PAD_LEFT),
                'ano' => $m[4],
                'raw' => $decoded,
            ];
        }

        // formato: 45273916000153/2026/000001
        if (preg_match('~^(\d{14})/(\d{4})/(\d+)$~', $decoded, $m)) {
            return [
                'cnpj' => $m[1],
                'unidade' => null,
                'seq' => str_pad($m[3], 6, '0', STR_PAD_LEFT),
                'ano' => $m[2],
                'raw' => $decoded,
            ];
        }

        return null;
    }

    protected function buildEditalUrl(array $pregao): ?string
    {
        $numeroControle = $pregao['numeroControlePNCP'] ?? $pregao['numeroControlePncp'] ?? null;
        $parts = $this->parseNumeroControle($numeroControle);

        if (!$parts) return null;

        // padrão correto:
        // https://pncp.gov.br/app/editais/{cnpj}/{ano}/{seq}
        return 'https://pncp.gov.br/app/editais/' . $parts['cnpj'] . '/' . $parts['ano'] . '/' . $parts['seq'];
    }

    protected function buildContratosUrl(array $pregao): ?string
    {
        $numeroControle = $pregao['numeroControlePNCP'] ?? $pregao['numeroControlePncp'] ?? null;
        $parts = $this->parseNumeroControle($numeroControle);

        if (!$numeroControle) return null;

        // padrão pedido:
        // https://pncp.gov.br/app/contratos?q=18431312000620-1-000283%2F2025
        // (mantém -unidade- e /ano, com encoding do /)
        if ($parts && $parts['unidade']) {
            $q = $parts['cnpj'] . '-' . $parts['unidade'] . '-' . $parts['seq'] . '/' . $parts['ano'];
            return 'https://pncp.gov.br/app/contratos?q=' . rawurlencode($q);
        }

        return 'https://pncp.gov.br/app/contratos?q=' . rawurlencode(urldecode((string)$numeroControle));
    }

    protected function inferArquivoSeq(array $docs): int
    {
        foreach ($docs as $d) {
            if (!is_array($d)) continue;

            foreach (['sequencialArquivo', 'sequencialDocumento', 'idArquivo', 'sequencial'] as $k) {
                if (!empty($d[$k]) && is_numeric($d[$k])) {
                    return (int) $d[$k];
                }
            }
        }

        // fallback solicitado
        return 1;
    }

    protected function buildEditalDownloadUrl(array $pregao, array $docs): ?string
    {
        $numeroControle = $pregao['numeroControlePNCP'] ?? $pregao['numeroControlePncp'] ?? null;
        $parts = $this->parseNumeroControle($numeroControle);

        if (!$parts) return null;

        $arquivo = $this->inferArquivoSeq($docs);

        // padrão:
        // https://pncp.gov.br/pncp-api/v1/orgaos/{cnpj}/compras/{ano}/{seq}/arquivos/{arquivo}
        return 'https://pncp.gov.br/pncp-api/v1/orgaos/'
            . $parts['cnpj']
            . '/compras/'
            . $parts['ano']
            . '/'
            . $parts['seq']
            . '/arquivos/'
            . $arquivo;
    }
}
