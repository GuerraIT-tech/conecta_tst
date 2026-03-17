<?php

namespace App\Filament\Resources\AreaInterestResource\Pages;

use App\Filament\Resources\AreaInterestResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageAreaInterests extends ManageRecords
{
    protected static string $resource = AreaInterestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
