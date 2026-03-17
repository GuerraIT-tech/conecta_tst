<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class PncpService
{
    // Mesma base do seu Python (Compras Dados Abertos)
    public const ENDPOINT_CONTRATACOES = 'https://dadosabertos.compras.gov.br/modulo-contratacoes/1_consultarContratacoes_PNCP_14133';

    // Itens / Resultados (manual API Compras)
    public const ENDPOINT_ITENS = 'https://dadosabertos.compras.gov.br/modulo-contratacoes/2_consultarItensContratacoes_PNCP_14133';
    public const ENDPOINT_RESULTADOS_ITENS = 'https://dadosabertos.compras.gov.br/modulo-contratacoes/3_consultarResultadoItensContratacoes_PNCP_14133';

    // Para listar/baixar documentos (PNCP)
    // (mantém fallback porque em alguns ambientes a rota muda)
    public const PNCP_API_BASES = [
        'https://pncp.gov.br/api/pncp/v1',
        'https://pncp.gov.br/pncp-api/v1',
    ];

    /**
     * Monta link do edital no portal:
     * https://pncp.gov.br/app/editais/{cnpj}/{ano}/{sequencial}
     */
    public function editalUrlFromNumeroControle(?string $numeroControlePncp): ?string
    {
        $parts = $this->parseNumeroControle($numeroControlePncp);
        if (!$parts) return null;

        return "https://pncp.gov.br/app/editais/{$parts['cnpj']}/{$parts['ano']}/{$parts['sequencial']}";
    }

    /**
     * numeroControlePNCP geralmente vem como:
     * 00394494000136-1-000786/2025
     */
    public function parseNumeroControle(?string $numeroControlePncp): ?array
    {
        if (!$numeroControlePncp) return null;

        if (!preg_match('/^(\d{14})-\d+-0*(\d+)\/(\d{4})$/', trim($numeroControlePncp), $m)) {
            return null;
        }

        return [
            'cnpj' => $m[1],
            'sequencial' => (int) $m[2],
            'ano' => (int) $m[3],
        ];
    }

    /**
     * Busca itens via Dados Abertos (por idCompra).
     * Retorna array de itens (campo "resultado").
     */
    public function fetchItensByIdCompra(string $idCompra, int $pagina = 1, int $tamanhoPagina = 200): array
    {
        $tamanhoPagina = min(max($tamanhoPagina, 1), 500);

        $resp = Http::timeout(15)
            ->acceptJson()
            ->get(self::ENDPOINT_ITENS, [
                'pagina' => $pagina,
                'tamanhoPagina' => $tamanhoPagina,
                'idCompra' => $idCompra,
            ]);

        if (!$resp->successful()) {
            return [];
        }

        return $resp->json('resultado') ?? [];
    }

    /**
     * Lista documentos do PNCP (metadados) e já devolve com URL de download.
     */
    public function fetchDocumentosByNumeroControle(?string $numeroControlePncp): array
    {
        $parts = $this->parseNumeroControle($numeroControlePncp);
        if (!$parts) return [];

        $cacheKey = "pncp_docs:{$parts['cnpj']}:{$parts['ano']}:{$parts['sequencial']}";

        return Cache::remember($cacheKey, now()->addHours(6), function () use ($parts) {
            $docs = [];

            foreach (self::PNCP_API_BASES as $base) {
                $resp = Http::timeout(20)
                    ->acceptJson()
                    ->get($base . "/orgaos/{$parts['cnpj']}/compras/{$parts['ano']}/{$parts['sequencial']}/arquivos");

                if ($resp->successful()) {
                    $docs = $resp->json() ?? [];
                    break;
                }
            }

            // Normaliza e cria download_url quando possível
            $normalized = [];
            foreach ($docs as $d) {
                $seqDoc = $d['sequencialDocumento'] ?? $d['sequencial'] ?? $d['id'] ?? null;

                $normalized[] = [
                    'sequencialDocumento' => $seqDoc,
                    'titulo' => $d['titulo'] ?? $d['nome'] ?? $d['descricao'] ?? ('Documento ' . ($seqDoc ?? '')),
                    'tipo' => $d['tipoDocumento'] ?? $d['tipo'] ?? null,
                    'dataPublicacao' => $d['dataPublicacao'] ?? $d['data'] ?? null,
                    'download_url' => $seqDoc
                        ? $this->downloadDocumentoUrl($parts['cnpj'], $parts['ano'], $parts['sequencial'], $seqDoc)
                        : null,
                ];
            }

            return $normalized;
        });
    }

    public function downloadDocumentoUrl(string $cnpj, int $ano, int $sequencialCompra, $sequencialDocumento): ?string
    {
        // tenta base principal (api/pncp) - se seu ambiente usar a outra base, troca aqui
        return "https://pncp.gov.br/api/pncp/v1/orgaos/{$cnpj}/compras/{$ano}/{$sequencialCompra}/arquivos/{$sequencialDocumento}";
    }
}
