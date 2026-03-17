<?php

namespace App\Filament\Resources;
use App\Services\PncpService;
use App\Filament\Resources\RadarResource\Pages;
use App\Models\Radar;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RadarResource extends Resource
{
    protected static ?string $model = Radar::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $slug = 'radar';
    protected static ?string $navigationLabel = 'Radar';
    protected static ?string $pluralLabel = 'Radar';
    protected static ?string $navigationGroup = 'Inteligência';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('titulo')
                ->required()
                ->maxLength(255),

            Forms\Components\Select::make('situacao')
                ->options([
                    'Novo' => 'Novo',
                    'Urgente' => 'Urgente',
                    'Em Andamento' => 'Em Andamento',
                    'Concluído' => 'Concluído',
                ])
                ->searchable()
                ->required(),

            Forms\Components\Select::make('orgao')
                ->options([
                    'Prefeitura Municipal' => 'Prefeitura Municipal',
                    'Governo do Estado de Minas Gerais' => 'Governo do Estado de Minas Gerais',
                    'Ministério Público' => 'Ministério Público',
                ])
                ->searchable()
                ->required(),

            Forms\Components\Select::make('modality_id')
                ->label('Modalidade')
                ->relationship('modality', 'name')
                ->searchable()
                ->preload()
                ->required(),

            Forms\Components\Select::make('state_id')
                ->label('Estado')
                ->relationship(
                    name: 'state',
                    titleAttribute: 'name',
                    modifyQueryUsing: fn ($query) => $query->select('id', 'name', 'uf')
                )
                ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} - {$record->uf}")
                ->searchable()
                ->preload()
                ->required(),

            Forms\Components\Select::make('area_interest_id')
                ->label('Área de Interesse')
                ->relationship('area_interest', 'name')
                ->searchable()
                ->preload()
                ->required(),

            Forms\Components\TextInput::make('valor')
                ->prefix('R$')
                ->numeric(),

            Forms\Components\TextInput::make('relevancia')
                ->required()
                ->numeric()
                ->default(0),

            Forms\Components\DateTimePicker::make('data_hora_encerramento'),

            Forms\Components\Textarea::make('descricao')
                ->rows(6)
                ->columnSpanFull(),

            Forms\Components\Textarea::make('observacoes')
                ->rows(6)
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            // cards/grid no padrão Filament
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            // clicar no card abre o "view"
            ->recordAction('view')
            ->persistFiltersInSession(false)
            ->columns([
                // Card principal
                ViewColumn::make('pncp_card')
                    ->view('filament.tables.columns.pncp-radar-card'),

                // (opcional) colunas escondidas, úteis se você quiser usar busca depois
                Tables\Columns\TextColumn::make('titulo')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('orgao')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('descricao')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('numero_controle_pncp')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('situacao')
                    ->label('')
                    ->placeholder('Situação')
                    ->options([
                        'Novo' => 'Novo',
                        'Urgente' => 'Urgente',
                        'Em Andamento' => 'Em Andamento',
                        'Concluído' => 'Concluído',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (empty($data['value'])) return $query;
                        return $query->where('situacao', $data['value']);
                    }),

                SelectFilter::make('orgao')
                    ->label('')
                    ->placeholder('Selecione o órgão')
                    ->options(fn () => Radar::query()->distinct()->pluck('orgao', 'orgao')->toArray())
                    ->query(function (Builder $query, array $data): Builder {
                        if (empty($data['value'])) return $query;
                        return $query->where('orgao', $data['value']);
                    }),

                SelectFilter::make('valor')
                    ->label('')
                    ->placeholder('Valor')
                    ->options([
                        '0-1000' => '0 - 1.000',
                        '1001-10000' => '1.001 - 10.000',
                        '10001-50000' => '10.001 - 50.000',
                        '50001+' => '50.001+',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (!isset($data['value'])) return $query;

                        return match ($data['value']) {
                            '0-1000' => $query->whereBetween('valor', [0, 1000]),
                            '1001-10000' => $query->whereBetween('valor', [1001, 10000]),
                            '10001-50000' => $query->whereBetween('valor', [10001, 50000]),
                            '50001+' => $query->where('valor', '>', 50000),
                            default => $query,
                        };
                    }),

                // Corrigido: era "Relevancia" com R maiúsculo, agora bate com o campo "relevancia"
                SelectFilter::make('relevancia')
                    ->label('')
                    ->placeholder('Selecione a relevância')
                    ->options([
                        '0-10' => '0 - 10',
                        '11-50' => '11 - 50',
                        '51-100' => '51 - 100',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (!isset($data['value'])) return $query;

                        return match ($data['value']) {
                            '0-10' => $query->whereBetween('relevancia', [0, 10]),
                            '11-50' => $query->whereBetween('relevancia', [11, 50]),
                            '51-100' => $query->whereBetween('relevancia', [51, 100]),
                            default => $query,
                        };
                    }),

                SelectFilter::make('data_hora_encerramento')
                    ->label('')
                    ->placeholder('Data de Encerramento')
                    ->options([
                        'today' => 'Hoje',
                        'this_week' => 'Esta Semana',
                        'this_month' => 'Este Mês',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (!isset($data['value'])) return $query;

                        return match ($data['value']) {
                            'today' => $query->whereDate('data_hora_encerramento', today()),
                            'this_week' => $query->whereBetween('data_hora_encerramento', [now()->startOfWeek(), now()->endOfWeek()]),
                            'this_month' => $query->whereBetween('data_hora_encerramento', [now()->startOfMonth(), now()->endOfMonth()]),
                            default => $query,
                        };
                    }),

                SelectFilter::make('tempo_restante')
                    ->label('')
                    ->placeholder('Tempo Restante')
                    ->options([
                        '0-1 day' => 'Menos de 1 dia',
                        '1-7 days' => '1 a 7 dias',
                        '8-30 days' => '8 a 30 dias',
                        '30+ days' => 'Mais de 30 dias',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (!isset($data['value'])) return $query;

                        $now = now();

                        return match ($data['value']) {
                            '0-1 day' => $query->whereBetween('data_hora_encerramento', [$now, $now->copy()->addDay()]),
                            '1-7 days' => $query->whereBetween('data_hora_encerramento', [$now->copy()->addDay(), $now->copy()->addDays(7)]),
                            '8-30 days' => $query->whereBetween('data_hora_encerramento', [$now->copy()->addDays(8), $now->copy()->addDays(30)]),
                            '30+ days' => $query->where('data_hora_encerramento', '>', $now->copy()->addDays(30)),
                            default => $query,
                        };
                    }),

                SelectFilter::make('created_at')
                    ->label('')
                    ->placeholder('Data de Criação')
                    ->options([
                        'today' => 'Hoje',
                        'this_week' => 'Esta Semana',
                        'this_month' => 'Este Mês',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (!isset($data['value'])) return $query;

                        return match ($data['value']) {
                            'today' => $query->whereDate('created_at', today()),
                            'this_week' => $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]),
                            'this_month' => $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]),
                            default => $query,
                        };
                    }),

                SelectFilter::make('updated_at')
                    ->label('')
                    ->placeholder('Data de Atualização')
                    ->options([
                        'today' => 'Hoje',
                        'this_week' => 'Esta Semana',
                        'this_month' => 'Este Mês',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (!isset($data['value'])) return $query;

                        return match ($data['value']) {
                            'today' => $query->whereDate('updated_at', today()),
                            'this_week' => $query->whereBetween('updated_at', [now()->startOfWeek(), now()->endOfWeek()]),
                            'this_month' => $query->whereBetween('updated_at', [now()->startOfMonth(), now()->endOfMonth()]),
                            default => $query,
                        };
                    }),

                SelectFilter::make('modality_id')
                    ->label('')
                    ->placeholder('Modalidade')
                    ->options(\App\Models\Modality::query()->pluck('name', 'id')->toArray())
                    ->query(function (Builder $query, array $data): Builder {
                        if (empty($data['value'])) return $query;
                        return $query->where('modality_id', $data['value']);
                    }),

                SelectFilter::make('area_interest_id')
                    ->label('')
                    ->placeholder('Área de Interesse')
                    ->options(\App\Models\AreaInterest::query()->pluck('name', 'id')->toArray())
                    ->query(function (Builder $query, array $data): Builder {
                        if (empty($data['value'])) return $query;
                        return $query->where('area_interest_id', $data['value']);
                    }),

                SelectFilter::make('state_id')
                    ->label('')
                    ->placeholder('Estado')
                    ->options(\App\Models\State::query()->pluck('name', 'id')->toArray())
                    ->query(function (Builder $query, array $data): Builder {
                        if (empty($data['value'])) return $query;
                        return $query->where('state_id', $data['value']);
                    }),
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormColumns(5)
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('')
                    ->modalHeading(fn ($record) => $record->orgao ?? 'Detalhes')
                    ->modalWidth('7xl')
                    ->modalContent(function ($record) {
                        /** @var PncpService $pncp */
                        $pncp = app(PncpService::class);

                        $itens = $record->pncp_id_compra
                            ? $pncp->fetchItensByIdCompra($record->pncp_id_compra)
                            : [];

                        $documentos = $record->numero_controle_pncp
                            ? $pncp->fetchDocumentosByNumeroControle($record->numero_controle_pncp)
                            : [];

                        $editalUrl = $pncp->editalUrlFromNumeroControle($record->numero_controle_pncp);

                        return view('filament.radar.pncp-details', [
                            'record' => $record,
                            'itens' => $itens,
                            'documentos' => $documentos,
                            'editalUrl' => $editalUrl,
                        ]);
                    }),
            ])
            ->searchable(false)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRadars::route('/'),
            'create' => Pages\CreateRadar::route('/create'),
            'edit' => Pages\EditRadar::route('/{record}/edit'),
        ];
    }

    public static function getTableQuery(): Builder
    {
        // melhora performance pra cards (evita N+1 se o card acessar relações)
        return parent::getTableQuery()
            ->with(['modality', 'area_interest', 'state']);
    }
}
