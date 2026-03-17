<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BidsResource\Pages;
use App\Filament\Resources\BidsResource\RelationManagers;
use App\Models\Bids;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Actions\CreateAction;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;

class BidsResource extends Resource
{
    protected static ?string $model = Bids::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Licitações';
    protected static ?string $pluralLabel = 'Licitações';
    protected static ?string $slug = 'licitacoes';
    protected static ?string $navigationGroup = 'Fundação';

    // protected static ?string $recordTitleAttribute = 'bidding_number';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['bidding_number', 'auctioneer_email', 'registration_email', 'auctioneer_name'];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informações Básicas')
                    ->schema([
                        Forms\Components\TextInput::make('requesting_agency')
                            ->label('Órgão Licitante')
                            ->translateLabel()
                            ->required(),

                        Forms\Components\Select::make('bidding_modality')
                            ->label('Modalidade')
                            ->options([
                                'Concorrência Eletrônica' => 'Concorrência Eletrônica',
                                'Pregão Eletrônico' => 'Pregão Eletrônico',
                                'Pregão Eletrônico Registro de Preços' => 'Pregão Eletrônico Registro de Preços',
                                'Dispensa de Licitação' => 'Dispensa de Licitação',
                            ])
                            ->required(),

                        Forms\Components\TextInput::make('bidding_number')
                            ->label('Número da Licitação')
                            ->required(),

                        Forms\Components\TextInput::make('uasg_number')
                            ->label('Número da UASG')
                            ->nullable(),
                    ])->columns(2),

                Forms\Components\Section::make('Datas e Contatos')
                    ->schema([
                        Forms\Components\DateTimePicker::make('bidding_stage_start')
                            ->label('Início da Etapa de Lances'),

                        Forms\Components\DateTimePicker::make('registration_deadline')
                            ->label('Término do Credenciamento'),

                        Forms\Components\TextInput::make('registration_email')
                            ->label('Email para Credenciamento')
                            ->suffix('@')
                            ->email(),

                        Forms\Components\TextInput::make('auctioneer_name')
                            ->label('Nome do Pregoeiro')
                            ->suffix('Sr. / Sra.'),

                        Forms\Components\TextInput::make('auctioneer_email')
                            ->label('Email do Pregoeiro')
                            ->suffix('@')
                            ->email(),

                        Forms\Components\TextInput::make('platform_email')
                            ->label('Email da Plataforma Para Etapa de Lances')
                            ->suffix('@')
                            ->email(),

                        Forms\Components\TextInput::make('auctioneer_phone')
                            ->label('Telefone do Pregoeiro')
                            ->mask('(99) 99999-9999'),
                    ])->columns(2),

                Forms\Components\Section::make('Itens da Licitação')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->label('Itens')
                            ->schema([
                                Forms\Components\TextInput::make('item')
                                    ->label('Item')
                                    ->required(),
                                Forms\Components\Textarea::make('description')
                                    ->label('Descrição')
                                    ->required(),
                                Forms\Components\TextInput::make('quantity')
                                    ->label('Quantidade')
                                    ->numeric()
                                    ->required(),
                                Forms\Components\TextInput::make('max_price')
                                    ->label('Preço Máximo')
                                    ->suffix('R$')
                                    ->numeric()
                                    ->nullable(),
                            ])
                            ->columnSpan('full')
                            ->columns(4)
                            ->defaultItems(1),
                    ]),

                Forms\Components\Section::make('Documentos e Declarações')
                    ->schema([
                        Forms\Components\Textarea::make('required_documents')
                            ->label('Documentos Solicitados')
                            ->rows(5)
                            ->nullable(),

                        Forms\Components\Textarea::make('required_declarations')
                            ->label('Declarações Solicitadas')
                            ->rows(5)
                            ->nullable(),
                    ]),

                Forms\Components\Section::make('Tipo da Licitação')
                    ->schema([
                        Forms\Components\Select::make('bidding_type')
                            ->label('Tipo de Licitação')
                            ->options([
                                'Aberta' => 'Aberta',
                                'Exclusiva (ME/EPP)' => 'Exclusiva (ME/EPP)',
                                'Mista' => 'Mista',
                            ])
                            ->default('Aberta')
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                /*Tables\Columns\TextColumn::make('document_id')
                    ->numeric()
                    ->translateLabel()
                    ->sortable(),*/
                Tables\Columns\IconColumn::make('favorito')
                         ->label('')
                        ->tooltip(fn (Bids $record): string => auth()->user()->favoriteBids->contains($record->id) ? 'Favorito' : 'Não favorito')
                        ->icon(fn (Bids $record): string => auth()->user()->favoriteBids->contains($record->id) ? 'heroicon-s-star' : 'heroicon-o-star')
                        ->color(fn (Bids $record): string => auth()->user()->favoriteBids->contains($record->id) ? 'warning' : 'gray')
                        ->getStateUsing(fn (Bids $record): bool => auth()->user()->favoriteBids->contains($record->id))
                        // usa getStateUsing quando quiser um valor booleano real, mas aqui usamos icon/color/tooltip diretamente
                        ->toggleable(false), // só para evitar comportamentos de toggle padrão
                Tables\Columns\TextColumn::make('requesting_agency')
                    ->label('Órgão Licitante')
                    ->searchable(),
                Tables\Columns\TextColumn::make('bidding_modality')
                    ->label('Modalidade')
                    ->searchable(),
                Tables\Columns\TextColumn::make('bidding_number')
                    ->label('Número da Licitação')
                    ->searchable(),
                Tables\Columns\TextColumn::make('uasg_number')
                    ->label('Número da UASG')
                    ->searchable(),
                Tables\Columns\TextColumn::make('bidding_stage_start')
                    ->label('Início da Etapa de Lances')
                    ->dateTime('d/m/Y H:m')
                    ->sortable(),
                Tables\Columns\TextColumn::make('registration_deadline')
                    ->label('Término do Credenciamento')
                    ->dateTime('d/m/Y H:m')
                    ->sortable(),
                Tables\Columns\TextColumn::make('platform_email')
                    ->label('Email do Pregoeiro')
                    ->searchable(),
                Tables\Columns\TextColumn::make('registration_email')
                    ->label('Email da Plataforma Para Etapa de Lances')
                    ->searchable(),
                Tables\Columns\TextColumn::make('auctioneer_name')
                    ->label('Nome do Pregoeiro')
                    ->searchable(),
                Tables\Columns\TextColumn::make('auctioneer_email')
                    ->label('Email do Pregoeiro')
                    ->searchable(),
                Tables\Columns\TextColumn::make('auctioneer_phone')
                    ->label('Telefone do Pregoeiro')
                    ->searchable(),
                Tables\Columns\TextColumn::make('bidding_type')
                    ->label('Tipo de Licitação'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // exemplo do filtro "Somente Favoritos" (toggle)
                Tables\Filters\Filter::make('favoritos')
                    ->label('Somente Favoritos')
                    ->query(fn (Builder $query): Builder => $query->whereHas('favoritedBy', fn ($q) => $q->where('user_id', auth()->id())))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ViewAction::make(),
                // Ação por linha pra favoritar/desfavoritar sem sair da listagem
                Action::make('toggleFavorite')
                    ->label(fn (Bids $record) => auth()->user()->favoriteBids->contains($record->id) ? 'Remover' : 'Favoritar')
                    ->icon(fn (Bids $record) => auth()->user()->favoriteBids->contains($record->id) ? 'heroicon-s-star' : 'heroicon-o-star')
                    ->color(fn (Bids $record) => auth()->user()->favoriteBids->contains($record->id) ? 'warning' : 'gray')
                    ->action(function (Bids $record, $livewire) {
                        $user = auth()->user();

                        if ($user->favoriteBids()->where('bid_id', $record->id)->exists()) {
                            $user->favoriteBids()->detach($record->id);
                            Notification::make()->title('Removido dos favoritos')->success()->send();
                        } else {
                            $user->favoriteBids()->attach($record->id);
                            Notification::make()->title('Adicionado aos favoritos')->success()->send();
                        }

                        // forçar refresh da tabela na UI
                        // se essa action estiver dentro de uma classe Livewire, use $this->emit('refreshTable')
                        // Aqui utilizamos um evento que o Filament entende:
                        $livewire->dispatch('refresh'); // forma simples de atualizar a listagem
                    })
                    ->requiresConfirmation(),
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
            'index' => Pages\ListBids::route('/'),
            'create' => Pages\CreateBids::route('/create'),
            'edit' => Pages\EditBids::route('/{record}/edit'),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Novo Usuário'), // <- Aqui você muda o nome do botão
        ];
    }

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()->with('favoritedBy');
    }
}
