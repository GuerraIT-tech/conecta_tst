<?php

namespace App\Filament\Resources\ModalityResource\Pages;

use App\Filament\Resources\ModalityResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageModalities extends ManageRecords
{
    protected static string $resource = ModalityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
