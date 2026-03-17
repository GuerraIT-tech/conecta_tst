<?php

namespace App\Services;

use App\Models\AreaInterest;
use App\Models\Modality;
use App\Models\Radar;
use App\Models\State;
use Carbon\Carbon;
use Illuminate\Support\Str;

class PncpRadarSyncService
{
    public function __construct(private readonly PncpPregaoService $pncp) {}

    public function sync(int $days = 10, string $codigoModalidade = '05'): int
    {
        $dataInicial = now()->subDays($days)->format('Y-m-d');
        $dataFinal = now()->format('Y-m-d');

        $pregoes = $this->pncp->fetchPregoes($dataInicial, $dataFinal, $codigoModalidade, 1, 500);

        // fallbacks (porque no seu form são required)
        $areaDefaultId = AreaInterest::firstOrCreate(['name' => 'Não informado'])->id;
        $modalityDefaultId = Modality::firstOrCreate(['name' => 'Não informado'])->id;
        $stateDefault = State::firstOrCreate(
            ['uf' => '--'],
            ['name' => 'Não informado', 'cod_uf' => 0]
        );

        $stateDefaultId = $stateDefault->id;
        $updated = 0;

        foreach ($pregoes as $p) {
            $idCompra = (string) ($p['idCompra'] ?? '');
            if ($idCompra === '') {
                // fallback se vier sem idCompra
                $idCompra = md5(($p['numeroControlePNCP'] ?? '') . ($p['objetoCompra'] ?? ''));
            }

            $status = $this->pncp->classify($p);

            // mapeia para o teu campo "situacao" atual (pra manter padrão do projeto)
            $situacao = match ($status) {
                'abertos' => 'Novo',
                'fechando' => 'Urgente',
                'fechado' => 'Concluído',
                default => 'Novo',
            };

            $uf = $p['unidadeOrgaoUfSigla'] ?? null;
            $stateId = $uf ? (State::where('uf', $uf)->value('id') ?? $stateDefaultId) : $stateDefaultId;

            $modalNome = $p['modalidadeNome'] ?? null;
            $modalityId = $modalNome ? (Modality::where('name', $modalNome)->value('id') ?? $modalityDefaultId) : $modalityDefaultId;

            $enc = !empty($p['dataEncerramentoPropostaPncp'])
                ? Carbon::parse($p['dataEncerramentoPropostaPncp'])->setTimezone(config('app.timezone'))->toDateTimeString()
                : null;

            Radar::updateOrCreate(
                ['pncp_id_compra' => $idCompra],
                [
                    'numero_controle_pncp' => $p['numeroControlePNCP'] ?? null,
                    'status_pncp' => $status,

                    'titulo' => Str::limit(($p['numeroControlePNCP'] ?? 'PNCP') . ' - ' . ($p['objetoCompra'] ?? ''), 255),
                    'situacao' => $situacao,
                    'orgao' => $p['orgaoEntidadeRazaoSocial'] ?? 'Não informado',

                    'modality_id' => $modalityId,
                    'state_id' => $stateId,
                    'area_interest_id' => $areaDefaultId,

                    'valor' => (float) ($p['valorTotalEstimado'] ?? 0),
                    'relevancia' => match ($status) {
                        'fechando' => 90,
                        'abertos' => 60,
                        'fechado' => 0,
                        default => 0,
                    },

                    'data_hora_encerramento' => $enc,
                    'descricao' => $p['objetoCompra'] ?? null,
                    'observacoes' => json_encode($p, JSON_UNESCAPED_UNICODE),
                ]
            );

            $updated++;
        }

        return $updated;
    }
}
