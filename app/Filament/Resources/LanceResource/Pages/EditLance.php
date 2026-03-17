<?php

namespace App\Filament\Resources\LanceResource\Pages;

use App\Filament\Resources\LanceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLance extends EditRecord
{
    protected static string $resource = LanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
