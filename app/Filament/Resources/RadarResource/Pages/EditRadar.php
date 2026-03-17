<?php

namespace App\Filament\Resources\RadarResource\Pages;

use App\Filament\Resources\RadarResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRadar extends EditRecord
{
    protected static string $resource = RadarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
