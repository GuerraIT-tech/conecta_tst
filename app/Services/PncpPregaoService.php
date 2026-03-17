<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class PncpPregaoService
{
    public const ENDPOINT = 'https://dadosabertos.compras.gov.br/modulo-contratacoes/1_consultarContratacoes_PNCP_14133';

    // Itens (Dados abertos)
    public const ENDPOINT_ITENS = 'https://dadosabertos.compras.gov.br/modulo-contratacoes/2_consultarItensContratacoes_PNCP_14133';

    // Documentos (PNCP) - pode variar por ambiente, então tem fallback
    public const PNCP_API_BASES = [
        'https://pncp.gov.br/api/pncp/v1',
        'https://pncp.gov.br/pncp-api/v1',
    ];

    public function fetchPregoes(?string $dataInicial = null, ?string $dataFinal = null, string $codigoModalidade = '05', int $pagina = 1, int $tamanhoPagina = 500): array
    {
        $dataInicial ??= now()->subDays(10)->format('Y-m-d');
        $dataFinal   ??= now()->format('Y-m-d');

        $tamanhoPagina = min(max($tamanhoPagina, 1), 500);

        $resp = Http::timeout(12)
            ->acceptJson()
            ->get(self::ENDPOINT, [
                'pagina' => $pagina,
                'tamanhoPagina' => $tamanhoPagina,
                'dataPublicacaoPncpInicial' => $dataInicial,
                'dataPublicacaoPncpFinal' => $dataFinal,
                'codigoModalidade' => $codigoModalidade,
            ]);

        if (! $resp->successful()) {
            return [];
        }

        return $resp->json('resultado') ?? [];
    }

    public function classify(array $pregao): string
    {
        $now = Carbon::now();

        $enc = $pregao['dataEncerramentoPropostaPncp'] ?? null;
        if (! $enc) return 'fechado';

        try {
            $encDate = Carbon::parse($enc);
        } catch (\Throwable) {
            return 'fechado';
        }

        if ($encDate->lt($now)) return 'fechado';

        if ($encDate->diffInDays($now) <= 3) return 'fechando';

        return 'abertos';
    }

    public function statusLabel(string $status): string
    {
        return match ($status) {
            'abertos' => 'ABERTO',
            'fechando' => 'FECHANDO EM BREVE',
            'fechado' => 'FECHADO',
            default => 'STATUS',
        };
    }

    // -------------------------------
    // Itens (por idCompra)
    // -------------------------------
    public function fetchItensByIdCompra(?string $idCompra, int $pagina = 1, int $tamanhoPagina = 200): array
    {
        if (!$idCompra) return [];

        $tamanhoPagina = min(max($tamanhoPagina, 1), 500);

        $resp = Http::timeout(15)
            ->acceptJson()
            ->get(self::ENDPOINT_ITENS, [
                'pagina' => $pagina,
                'tamanhoPagina' => $tamanhoPagina,
                'idCompra' => $idCompra,
            ]);

        if (! $resp->successful()) {
            return [];
        }

        return $resp->json('resultado') ?? [];
    }

    // -------------------------------
    // Link do edital (Portal PNCP)
    // numeroControlePNCP ex: 00394494000136-1-000786/2025
    // -------------------------------
    public function editalUrlFromNumeroControle(?string $numeroControlePncp): ?string
    {
        $parts = $this->parseNumeroControle($numeroControlePncp);
        if (!$parts) return null;

        return "https://pncp.gov.br/app/editais/{$parts['cnpj']}/{$parts['ano']}/{$parts['sequencial']}";
    }

    public function parseNumeroControle(?string $numeroControlePncp): ?array
    {
        if (!$numeroControlePncp) return null;

        // cnpj - ? - sequencial/ano
        if (!preg_match('/^(\d{14})-\d+-0*(\d+)\/(\d{4})$/', trim($numeroControlePncp), $m)) {
            return null;
        }

        return [
            'cnpj' => $m[1],
            'sequencial' => (int) $m[2],
            'ano' => (int) $m[3],
        ];
    }

    // -------------------------------
    // Documentos/arquivos (PNCP API)
    // -------------------------------
    public function fetchDocumentosByNumeroControle(?string $numeroControlePncp): array
    {
        $parts = $this->parseNumeroControle($numeroControlePncp);
        if (!$parts) return [];

        $cacheKey = "pncp_docs:{$parts['cnpj']}:{$parts['ano']}:{$parts['sequencial']}";

        return Cache::remember($cacheKey, now()->addHours(6), function () use ($parts) {
            $docsRaw = [];

            foreach (self::PNCP_API_BASES as $base) {
                $url = $base . "/orgaos/{$parts['cnpj']}/compras/{$parts['ano']}/{$parts['sequencial']}/arquivos";

                $resp = Http::timeout(20)->acceptJson()->get($url);

                if ($resp->successful()) {
                    $docsRaw = $resp->json() ?? [];
                    break;
                }
            }

            // Normaliza
            $docs = [];
            foreach ($docsRaw as $d) {
                $seqDoc = $d['sequencialDocumento'] ?? $d['sequencial'] ?? $d['id'] ?? null;

                $docs[] = [
                    'sequencialDocumento' => $seqDoc,
                    'titulo' => $d['titulo'] ?? $d['nome'] ?? $d['descricao'] ?? ('Documento ' . ($seqDoc ?? '')),
                    'tipo' => $d['tipoDocumento'] ?? $d['tipo'] ?? null,
                    'dataPublicacao' => $d['dataPublicacao'] ?? $d['data'] ?? null,
                    'download_url' => $seqDoc
                        ? $this->downloadDocumentoUrl($parts['cnpj'], $parts['ano'], $parts['sequencial'], $seqDoc)
                        : null,
                ];
            }

            return $docs;
        });
    }

    public function downloadDocumentoUrl(string $cnpj, int $ano, int $sequencialCompra, $sequencialDocumento): string
    {
        // download direto
        return "https://pncp.gov.br/api/pncp/v1/orgaos/{$cnpj}/compras/{$ano}/{$sequencialCompra}/arquivos/{$sequencialDocumento}";
    }
}
