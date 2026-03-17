<?php

namespace App\Filament\Resources\RadarResource\Pages;

use App\Filament\Resources\RadarResource;
use App\Models\Radar;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListRadars extends ListRecords
{
    protected static string $resource = RadarResource::class;

    public function getTabs(): array
    {
        return [
            'todos' => Tab::make('Todos')
                ->modifyQueryUsing(fn (Builder $query) => $query)
                ->badge(fn () => Radar::count())
                ->badgeColor('gray'),

            'abertos' => Tab::make('Inscrições Abertas')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status_pncp', 'abertos'))
                ->badge(fn () => Radar::where('status_pncp', 'abertos')->count())
                ->badgeColor('success'),

            'fechando' => Tab::make('Fechando em Breve')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status_pncp', 'fechando'))
                ->badge(fn () => Radar::where('status_pncp', 'fechando')->count())
                ->badgeColor('warning'),

            'fechado' => Tab::make('Fechados')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status_pncp', 'fechado'))
                ->badge(fn () => Radar::where('status_pncp', 'fechado')->count())
                ->badgeColor('danger'),
        ];
    }

    public function getDefaultActiveTab(): string|int|null
    {
        return 'todos';
    }
}
