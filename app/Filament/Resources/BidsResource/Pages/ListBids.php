<?php

namespace App\Filament\Resources\BidsResource\Pages;

use App\Filament\Resources\BidsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class ListBids extends ListRecords
{
    protected static string $resource = BidsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            Filter::make('favoritos')
                ->label('Somente Favoritos')
                ->toggle() // aparece como um switch
                ->query(function (Builder $query): Builder {
                    return $query->whereHas('favoritedBy', function ($q) {
                        $q->where('user_id', auth()->id());
                    });
                }),
        ];
    }
}
