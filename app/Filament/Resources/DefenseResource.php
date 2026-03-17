<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DefenseResource\Pages;
use App\Filament\Resources\DefenseResource\RelationManagers;
use App\Models\Defense;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DefenseResource extends Resource
{
    protected static ?string $model = Defense::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $slug = 'defesa';
    protected static ?string $navigationLabel = 'Defesa';
    protected static ?string $pluralLabel = 'Defesas';
    protected static ?string $navigationGroup = 'Execução';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Configuração da Contestação')
                    ->schema([
                        Forms\Components\Select::make('tipo_contestacao')
                        ->name('Tipo de Contestação')
                        ->options([
                                    'Inpugnação' => 'Inpugnação',
                                    'Recurso' => 'Recurso',
                                    'Representação' => 'Representação',
                                ])
                        ->required(),
                        Forms\Components\TextInput::make('numero_processo')
                            ->name('Número do Processo')
                            ->maxLength(255),
                        Forms\Components\Select::make('problema_motivo_contestacao')
                            ->name('Problema/Motivo da Contestação')
                            ->options([
                                'Erro na especificação técnica' => 'Erro na especificação técnica',
                                'Erro no orçamento estimativo' => 'Erro no orçamento estimativo',
                                'Violação Legal' => 'Violação Legal',
                                'Prazo Inadequado' => 'Prazo Inadequado',
                                'Exigência Desproporcional' => 'Exigência Desproporcional',
                            ]),
                        Forms\Components\DateTimePicker::make('prazo')
                            ->displayFormat('d/m/Y')
                            ->seconds(false)
                            ->native(false)
                            ->name('Prazo'),
                        Forms\Components\Select::make('orgao')
                            ->name('Orgão')
                            ->options([
                                        'Prefeitura Municipal' => 'Prefeitura Municipal',
                                        'Governo do Estado' => 'Governo do Estado',
                                        'Autarquia Federal' => 'Autarquia Federal',
                                    ])
                            ->required(),
                         Forms\Components\Select::make('status')
                            ->name('Status')
                            ->options([
                                        'Em Elaboração' => 'Em Elaboração',
                                        'Em Análise' => 'Em Análise',
                                        'Aguardando Decisão' => 'Aguardando Decisão',
                                        'Decidida' => 'Decidida',
                                        'Cancelada' => 'Cancelada',
                                        'Concluida' => 'Concluida',
                                    ])
                            ->required(),
                ])->columns(2),

                Forms\Components\Section::make('Editor de Documento')
                    ->schema([
                        Forms\Components\Textarea::make('documento')
                        ->name('Informações extras do documento')
                        ->rows(6)
                        ->maxLength(255),
                ]),


                Forms\Components\Section::make('Documentos de Suporte')
                    ->schema([
                        Forms\Components\FileUpload::make('arquivo')
                                ->label('Arquivo')
                                ->disk('public') // ou outro disco configurado no config/filesystems.php
                                ->directory('documentos') // pasta onde vai salvar
                                ->preserveFilenames() // mantém o nome original
                                ->maxSize(5120) // tamanho máximo em KB (aqui 5MB)
                                ->acceptedFileTypes(['application/pdf', 'image/*']), // PDF ou imagens
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tipo_contestacao'),
                Tables\Columns\TextColumn::make('numero_processo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('problema_motivo_contestacao')
                    ->searchable(),
                Tables\Columns\TextColumn::make('documento')
                    ->searchable(),
                Tables\Columns\TextColumn::make('prazo')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('orgao'),
                Tables\Columns\TextColumn::make('status')
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
            'index' => Pages\ListDefenses::route('/'),
            'create' => Pages\CreateDefense::route('/create'),
            'edit' => Pages\EditDefense::route('/{record}/edit'),
        ];
    }
}
