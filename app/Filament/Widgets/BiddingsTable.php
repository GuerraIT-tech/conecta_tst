<?php

namespace App\Filament\Widgets;

use App\Models\Bids;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class BiddingsTable extends BaseWidget
{

    protected static ?int $sort = 10;
    protected static ?string $heading = 'Licitações Cadastradas Recentemente';
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Bids::query()->latest()->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('requesting_agency')->label('Órgão Licitante'),
                Tables\Columns\TextColumn::make('bidding_modality')->label('Modalidade'),
                Tables\Columns\TextColumn::make('bidding_number')->label('Nº Licitação'),
                Tables\Columns\TextColumn::make('bidding_stage_start')->label('Início')->dateTime('d/m/Y H:i'),
                Tables\Columns\TextColumn::make('registration_deadline')->label('Término')->dateTime('d/m/Y H:i'),
            ]);
    }
}
