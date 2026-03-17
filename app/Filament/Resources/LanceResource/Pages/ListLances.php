<?php

namespace App\Filament\Resources\LanceResource\Pages;

use App\Filament\Resources\LanceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLances extends ListRecords
{
    protected static string $resource = LanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
