<?php

namespace App\Filament\Pages;

use App\Models\RadarPreference;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Page;

class RadarV2Config extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-adjustments-horizontal';
    protected static ?string $navigationGroup = 'Inteligência';
    protected static ?string $navigationLabel = 'Configurar Radar';
    protected static ?string $slug = 'radar-v2-config';
    protected static bool $shouldRegisterNavigation = false; // não aparecer no menu

    protected static string $view = 'filament.pages.radar-v2-config';

    public ?array $data = [];

    public const REGIONS = ['Sul', 'Sudeste', 'Centro-Oeste', 'Nordeste', 'Norte'];

    public const UFS = [
        'AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG',
        'PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO',
    ];

    public function mount(): void
    {
        abort_unless(auth()->check(), 403);

        $pref = RadarPreference::query()->where('user_id', auth()->id())->first();

        $this->form->fill([
            'keyword' => $pref->keyword ?? '',
            'regions' => $pref->regions ?? [],
            'ufs'     => $pref->ufs ?? [],
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->statePath('data')
            ->schema([
                TextInput::make('keyword')
                    ->label('Palavra-chave (para buscar no objeto do edital)')
                    ->helperText('Ex: "uniformes e camisetas" (você pode usar vírgula para várias: "uniforme, camiseta")')
                    ->nullable()
                    ->maxLength(255),

                CheckboxList::make('regions')
                    ->label('Regiões')
                    ->options(array_combine(self::REGIONS, self::REGIONS))
                    ->columns(3),

                Select::make('ufs')
                    ->label('UFs (inclui DF)')
                    ->multiple()
                    ->searchable()
                    ->options(array_combine(self::UFS, self::UFS))
                    ->helperText('Se você selecionar UFs e também Regiões, o filtro final será a interseção (UF ∩ Região).'),
            ]);
    }

    public function save(): void
    {
        abort_unless(auth()->check(), 403);

        $state = $this->form->getState();

        RadarPreference::updateOrCreate(
            ['user_id' => auth()->id()],
            [
                'keyword' => trim($state['keyword'] ?? ''),
                'regions' => array_values($state['regions'] ?? []),
                'ufs'     => array_values($state['ufs'] ?? []),
            ]
        );

        // Vai direto pro radar (e a tela de config não aparece mais automaticamente)
        $this->redirect(RadarV2::getUrl());
    }
}
