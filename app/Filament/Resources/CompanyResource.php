<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanyResource\Pages;
use App\Filament\Resources\CompanyResource\RelationManagers;
use App\Models\Company;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Forms\Components\Card;
use CodeWithDennis\FilamentSimpleAlert\SimpleAlert;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tab;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\TextInput\Mask;

class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $modelLabel = 'Cliente';
    protected static ?string $pluralModelLabel = 'Clientes';
    protected static ?string $slug = 'clientes';
    protected static ?string $navigationGroup = 'Fundação';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['cpf', 'rg', 'email', 'secondary_email', 'cnpj', 'corporate_name', 'trade_name'];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                 Tabs::make('Cadastro de Empresa')
                    ->columnSpanFull()
                    ->tabs([
                        Tabs\Tab::make('Informações Básicas')
                            ->schema([
                                Forms\Components\Select::make('tipo_pessoa')
                                    ->label('Tipo de Pessoa')
                                    ->options([
                                        'fisica' => 'Pessoa Física',
                                        'juridica' => 'Pessoa Jurídica',
                                    ])
                                    ->live() // necessário para atualizar o formulário em tempo real
                                    ->reactive()
                                    ->disabled(fn ($record) => $record !== null)
                                    ->afterStateHydrated(function (callable $set, $state, $record) {
                                        if (! $record) {
                                            return;
                                        }

                                        if ($record->trade_name != null) {
                                            $set('tipo_pessoa', 'fisica');
                                        } elseif ($record->corporate_name != null) {
                                            $set('tipo_pessoa', 'juridica');
                                        }
                                    })
                                    ->required(),

                                TextInput::make('trade_name')
                                    ->label('Nome Completo')
                                    ->visible(fn (callable $get) => $get('tipo_pessoa') === 'fisica')
                                    ->required(fn (callable $get) => $get('tipo_pessoa') === 'fisica'),

                                TextInput::make('cpf')
                                    ->label('CPF')
                                    ->mask('999.999.999-99')
                                    ->placeholder('000.000.000-00')
                                    ->visible(fn (callable $get) => $get('tipo_pessoa') === 'fisica')
                                    ->required(fn (callable $get) => $get('tipo_pessoa') === 'fisica'),

                                TextInput::make('rg')
                                    ->label('RG')
                                    ->visible(fn (callable $get) => $get('tipo_pessoa') === 'fisica'),

                                TextInput::make('cnpj')
                                    ->label('CNPJ')
                                    ->mask('99.999.999/9999-99')
                                    ->placeholder('00.000.000/0000-00')
                                    ->visible(fn (callable $get) => $get('tipo_pessoa') === 'juridica')
                                    ->required(fn (callable $get) => $get('tipo_pessoa') === 'juridica')
                                    ->afterStateHydrated(function ($state, callable $set, $record) {
                                        if ($record && $record->cnpj) {
                                            $set('cnpj', $record->cnpj);
                                        }
                                    })
                                    ->reactive(),

                                TextInput::make('corporate_name')
                                    ->label('Razão Social Completa')
                                    ->visible(fn (callable $get) => $get('tipo_pessoa') === 'juridica')
                                    ->required(fn (callable $get) => $get('tipo_pessoa') === 'juridica')
                                    ->afterStateHydrated(function ($state, callable $set, $record) {
                                        if ($record && $record->corporate_name) {
                                            $set('corporate_name', $record->corporate_name);
                                        }
                                    })
                                    ->reactive(),

                                TextInput::make('trade_name')
                                    ->label('Nome Fantasia')
                                    ->visible(fn (callable $get) => $get('tipo_pessoa') === 'juridica')
                                    ->required(fn (callable $get) => $get('tipo_pessoa') === 'juridica')
                                    ->afterStateHydrated(function ($state, callable $set, $record) {
                                        if ($record && $record->trade_name) {
                                            $set('trade_name', $record->trade_name);
                                        }
                                    })
                                    ->reactive(),

                                TextInput::make('state_registration')
                                    ->label('Inscrição Estadual (IE)')
                                    ->visible(fn (callable $get) => $get('tipo_pessoa') === 'juridica')
                                    ->afterStateHydrated(function ($state, callable $set, $record) {
                                        if ($record && $record->state_registration) {
                                            $set('state_registration', $record->state_registration);
                                        }
                                    })
                                    ->reactive(),

                                TextInput::make('municipal_registration')
                                    ->label('Inscrição Municipal (IM)')
                                    ->visible(fn (callable $get) => $get('tipo_pessoa') === 'juridica')
                                    ->afterStateHydrated(function ($state, callable $set, $record) {
                                        if ($record && $record->municipal_registration) {
                                            $set('municipal_registration', $record->municipal_registration);
                                        }
                                    })
                                    ->reactive(),

                                TextInput::make('company_size')
                                    ->label('Porte da Empresa')
                                    ->visible(fn (callable $get) => $get('tipo_pessoa') === 'juridica')
                                    ->afterStateHydrated(function ($state, callable $set, $record) {
                                        if ($record && $record->company_size) {
                                            $set('company_size', $record->company_size);
                                        }
                                    })
                                    ->reactive(),

                                TextInput::make('company_activities')
                                    ->label('Atividades da Empresa')
                                    ->visible(fn (callable $get) => $get('tipo_pessoa') === 'juridica')
                                    ->afterStateHydrated(function ($state, callable $set, $record) {
                                        if ($record && $record->company_activities) {
                                            $set('company_activities', $record->company_activities);
                                        }
                                    })
                                    ->reactive(),

                                Forms\Components\DateTimePicker::make('opening_date')
                                    ->name('Data de Abertura')
                                    ->placeholder('dd/mm/aaaa')
                                    ->displayFormat('d/m/Y')
                                    ->native(false)
                                    ->seconds(false)
                                    ->visible(fn (callable $get) => $get('tipo_pessoa') === 'juridica')
                                    ->afterStateHydrated(function ($state, callable $set, $record) {
                                        if ($record && $record->opening_date) {
                                            $set('opening_date', $record->opening_date);
                                        }
                                    })
                                    ->reactive(),

                                TextInput::make('share_capital')
                                    ->label('Capital Social')
                                    ->prefix('R$ ')
                                    ->visible(fn (callable $get) => $get('tipo_pessoa') === 'juridica')
                                    ->afterStateHydrated(function ($state, callable $set, $record) {
                                        if ($record && $record->share_capital) {
                                            $set('share_capital', $record->share_capital);
                                        }
                                    })
                                    ->reactive(),
                            ])->columns(2),

                        Tabs\Tab::make('Endereço')
                            ->schema([
                                 TextInput::make('zip_code')
                                    ->label('CEP')
                                    ->mask('99999-999')
                                    ->required(),

                                TextInput::make('address')
                                    ->label('Rua')
                                    ->required(),

                                TextInput::make('number')
                                    ->label('Número')
                                    ->required(),

                                TextInput::make('complement')
                                    ->label('Complemento'),

                                TextInput::make('district')
                                    ->label('Bairro')
                                    ->required(),

                                TextInput::make('city')
                                    ->label('Cidade')
                                    ->required(),

                                TextInput::make('state')
                                    ->label('Estado (UF)')
                                    ->maxLength(2)
                                    ->required(),
                            ])->columns(3),

                        Tabs\Tab::make('Contatos')
                            ->schema([
                                TextInput::make('phone')
                                    ->label('Telefone de Fixo')
                                    ->mask('(99) 9999-9999')
                                    ->required(),

                                TextInput::make('mobile_phone')
                                    ->label('Telefone de Celular')
                                    ->mask('(99) 99999-9999')
                                    ->required(),

                                TextInput::make('email')
                                    ->label('E-mail Primário')
                                    ->suffix('@')
                                    ->email()
                                    ->required(),

                                TextInput::make('secondary_email')
                                    ->label('E-mail Secundário')
                                    ->suffix('@')
                                    ->email()
                                    ->required(),

                                TextInput::make('website')
                                    ->url()
                                    ->prefix('http://')
                                    ->label('Website'),
                            ])->columns(2),
                        Tabs\Tab::make('Portais para Credenciamento')
                            ->schema([
                                 Card::make()
                                    ->schema([
                                        Forms\Components\Checkbox::make('comprasnet')
                                        ->label('Comprasnet (Governo Federal)')
                                        ->helperText('Portal de compras do Governo Federal'),
                                    ])
                                    ->columns(1)
                                    ->columnSpanFull(),
                                Card::make()
                                    ->schema([
                                        Forms\Components\Checkbox::make('bec')
                                        ->label('BEC (Governo de São Paulo)')
                                        ->helperText('Bolsa Eletrônica de Compras do Estado de SP'),
                                    ])
                                    ->columns(1)
                                    ->columnSpanFull(),
                                Card::make()
                                    ->schema([
                                        Forms\Components\Checkbox::make('pregao_eletronico')
                                        ->label('Pregão Eletrônico')
                                        ->helperText('Sistema para pregões eletrônicos'),
                                    ])
                                    ->columns(1)
                                    ->columnSpanFull(),
                                Card::make()
                                    ->schema([
                                        Forms\Components\Checkbox::make('sicaf')
                                        ->label('SICAF')
                                        ->helperText('Sistema de Cadastramento Unificado de Fornecedores'),
                                    ])
                                    ->columns(1)
                                    ->columnSpanFull(),
                                Card::make()
                                    ->schema([
                                        Forms\Components\Checkbox::make('pncp')
                                        ->label('Portal Nascional de Contratações Públicas')
                                        ->helperText('PNCP - Portal Unificado'),
                                    ])
                                    ->columns(1)
                                    ->columnSpanFull(),
                                Forms\Components\Textarea::make('additional_observations')
                                    ->name('Observações Adicionais')
                                    ->rows(6)
                                    ->maxLength(255),
                                Card::make()
                                    ->schema([
                                        Forms\Components\Placeholder::make('Próximos Passos Após Finalização')
                                            ->content(new HtmlString('
                                                🛡️ - Credenciamento automático nos portais selecionados<br>
                                                📄 - Credenciamento manual nos portais selecionados <br>
                                                💳 - Envio de e-mail de boas vindas
                                            '))
                                            ->extraAttributes([
                                                'class' => 'flex items-center gap-2 font-medium text-green-900'
                                            ])
                                    ])
                                     ->extraAttributes([
                                        'class' => 'bg-green-600 text-green-900 p-2 rounded-lg'
                                     ])
                                    ->visible(fn (callable $get) => $get('tipo_pessoa') === 'juridica')
                                    ->columns(1)
                                    ->columnSpanFull(),

                                Card::make()
                                    ->schema([
                                        Forms\Components\Placeholder::make('Documentos necessários para epp:')
                                            ->content(new HtmlString('
                                                - Contrato Social ou Estatuto<br>
                                                - Cartão CNPJ <br>
                                                - Certidões Negativas (Federal, Estadual, Municipal, Trabalhista, FGTS)
                                                - Balanço Patrimonial
                                            '))
                                            ->extraAttributes([
                                                'class' => 'flex items-center gap-2 font-medium text-green-900'
                                            ])
                                    ])
                                    ->extraAttributes([
                                        'class' => 'bg-green-600 text-green-900 p-2 rounded-lg'
                                     ])
                                    ->visible(fn (callable $get) => $get('tipo_pessoa') === 'fisica')
                                    ->columns(1)
                                    ->columnSpanFull(),
                            ])
                            ->visible(fn (callable $get) => $get('tipo_pessoa') === 'juridica')
                            ->columns(1),
                    ]),
                ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('corporate_name')
                    ->label('Razão Social Completa')
                    ->searchable(),
                Tables\Columns\TextColumn::make('trade_name')
                    ->label('Nome Fantasia')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cnpj')
                    ->label('CNPJ')
                    ->searchable(),
                Tables\Columns\TextColumn::make('state_registration')
                    ->label('Inscrição Estadual (IE)')
                    ->searchable(),
                Tables\Columns\TextColumn::make('municipal_registration')
                    ->label('Inscrição Municipal (IM)')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->label('Rua')
                    ->searchable(),
                Tables\Columns\TextColumn::make('number')
                    ->label('Número')
                    ->searchable(),
                Tables\Columns\TextColumn::make('complement')
                    ->label('Complemento')
                    ->searchable(),
                Tables\Columns\TextColumn::make('district')
                    ->label('Bairro')
                    ->searchable(),
                Tables\Columns\TextColumn::make('city')
                    ->label('Cidade')
                    ->searchable(),
                Tables\Columns\TextColumn::make('state')
                    ->label('Estado (UF)')
                    ->searchable(),
                Tables\Columns\TextColumn::make('zip_code')
                    ->label('CEP')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Telefone(s) de Contato')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('E-mail Corporativo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('website')
                    ->label('Website')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d/m/Y')
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
            'index' => Pages\ListCompanies::route('/'),
            'create' => Pages\CreateCompany::route('/create'),
            'edit' => Pages\EditCompany::route('/{record}/edit'),
        ];
    }
}
