<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LanceResource\Pages;
use App\Filament\Resources\LanceResource\RelationManagers;
use App\Models\Lance;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;

class LanceResource extends Resource
{
    protected static ?string $model = Lance::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $slug = 'lance';
    protected static ?string $navigationLabel = 'Lance';
    protected static ?string $pluralLabel = 'Lances';
    protected static ?string $navigationGroup = 'Execução';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                // 🔹 Agrupando em abas
            Tabs::make('Tabs')
                ->columnSpanFull()
                ->tabs([
                    Tabs\Tab::make('Estratégia')
                        ->schema([
                            Forms\Components\Select::make('company_id')
                                ->label('Cliente - (Inclua um Cliente se Necessário)')
                                ->placeholder('Selecione um cliente')
                                ->relationship('company', 'corporate_name') // supondo que o nome da empresa é 'name'
                                ->getOptionLabelFromRecordUsing(function ($record) {
                                    return $record->corporate_name ?: $record->trade_name;
                                })
                                ->searchable()
                                ->preload(),

                            Forms\Components\Select::make('tipo_participacao')
                                ->options([
                                    'Robo Automático' => 'Robô Automático',
                                    'Manual' => 'Manual',
                                    'Híbrido' => 'Híbrido',
                                ])
                                ->label('Tipo de Participação'),

                            Forms\Components\Select::make('estrategia_lance')
                                ->options([
                                    'Conservadora' => 'Conservadora',
                                    'Agressiva' => 'Agressiva',
                                    'Equilibrada' => 'Equilibrada',
                                ])
                                ->label('Estratégia de Lance'),

                            TextInput::make('lance_maximo')
                                ->numeric()
                                ->prefix('R$')
                                ->label('Lance Máximo'),

                            TextInput::make('limite_tempo')
                                ->numeric()
                                ->suffix('min')
                                ->label('Limite de Tempo (minutos)'),

                            TextInput::make('incremento_padrao')
                                ->numeric()
                                ->prefix('R$')
                                ->label('Incremento Padrão'),

                            TextInput::make('margem_seguranca')
                                ->numeric()
                                ->suffix('%')
                                ->label('Margem de Segurança'),

                            Toggle::make('incremento_automatico')
                                ->label('Incremento Automático')
                                ->columnSpan(3),

                            Toggle::make('notificacoes_tempo_real')
                                ->label('Notificações em Tempo Real'),
                        ])->columns(3),
                    Tabs\Tab::make('Proposta')
                        ->schema([
                         TextInput::make('licitacao_vencida')
                                ->label('Licitação Vencida'),

                            TextInput::make('lance_vencedor')
                                ->numeric()
                                ->prefix('R$')
                                ->label('Lance Vencedor'),

                            TextInput::make('prazo_entrega')
                                ->numeric()
                                ->suffix('dias')
                                ->label('Prazo de Entrega'),

                            Textarea::make('condicoes_pagamento')
                                ->label('Condições de Pagamento')
                                ->rows(4)
                                ->columnSpan(1),
                        ])->columns(3),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('company_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tipo_participacao')
                    ->searchable(),
                Tables\Columns\TextColumn::make('estrategia_lance')
                    ->searchable(),
                Tables\Columns\TextColumn::make('lance_maximo')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('limite_tempo')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('incremento_padrao')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('margem_seguranca')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('incremento_automatico')
                    ->boolean(),
                Tables\Columns\IconColumn::make('notificacoes_tempo_real')
                    ->boolean(),
                Tables\Columns\TextColumn::make('licitacao_vencida')
                    ->searchable(),
                Tables\Columns\TextColumn::make('lance_vencedor')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('prazo_entrega')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLances::route('/'),
            'create' => Pages\CreateLance::route('/create'),
            'edit' => Pages\EditLance::route('/{record}/edit'),
        ];
    }
}
