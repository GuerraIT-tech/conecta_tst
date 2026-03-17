<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\User;
use App\Models\Licitacao;
use App\Models\Venda;
use Filament\Widgets\StatsOverviewWidget\Card;
use Carbon\Carbon;
use App\Models\Bids;
use App\Models\Company;
use App\Models\Radar;

class DashboardStatsLicitacoes extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $companyAtivos = Company::where('is_active', true)->count();
        $usuariosAtivos = User::where('is_active', true)->count();
        $licitacoesAtivas = Bids::where('is_active', true)->count();
        $vendasMes = Radar::whereMonth('created_at', Carbon::now()->month)->count();

        // Exemplo de porcentagem (ajuste conforme lógica real)
        $percentAtivos = $companyAtivos > 0 ? round(($companyAtivos / $companyAtivos) * 100, 1) : 0;
        $percentUsuarios = $usuariosAtivos > 0 ? round(($usuariosAtivos / $usuariosAtivos) * 100, 1) : 0;
        $percentLicitacoes = $licitacoesAtivas > 0 ? round(($licitacoesAtivas / $licitacoesAtivas) * 100, 1) : 0;
        $percentVendas = $vendasMes > 0 ? 15 : 0; // Exemplo estático

        // Exemplo de dados de tendência (normalmente você buscaria do banco)
        $usuariosTrend = [10, 20, 25, 40, 60, 70, 120]; // crescimento
        $ativosTrend   = [50, 52, 55, 60, 58, 62, 64]; // estável
        $licitacoesTrend = [3, 6, 5, 9, 8, 12, 14];
        $vendasTrend = [5, 7, 12, 10, 15, 20, 25];

        return [
            Card::make('Número de Usuários', number_format($companyAtivos))
                ->description("$percentAtivos% dos clientes estão ativos")
                ->descriptionIcon('heroicon-m-user-group')
                ->chart($usuariosTrend) // 👈 sparkline
                ->color('primary'),

            Card::make('Usuários Ativos', number_format($usuariosAtivos))
                ->description("$percentUsuarios% dos usuários estão ativos")
                ->descriptionIcon('heroicon-m-check-circle')
                ->chart($ativosTrend)
                ->color('success'),

            Card::make('Quantidade de Licitações', $licitacoesAtivas)
                ->description("$percentLicitacoes% das licitações estão ativas")
                ->descriptionIcon('heroicon-m-document-text')
                ->chart($licitacoesTrend)
                ->color('warning'),

            Card::make('Vendas no Mês', number_format($vendasMes))
                ->description("Crescimento de {$percentVendas}% neste mês")
                ->descriptionIcon('heroicon-m-chart-bar')
                ->chart($vendasTrend)
                ->color('info'),
        ];

    }

    public function columns(): int | string | array
    {
        // 👇 Aqui você define 4 cards por linha
        return 5;
    }
}
