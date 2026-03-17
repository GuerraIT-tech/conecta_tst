<?php

namespace App\Filament\Resources\DefenseResource\Pages;

use App\Filament\Resources\DefenseResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDefense extends EditRecord
{
    protected static string $resource = DefenseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
