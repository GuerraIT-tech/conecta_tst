<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Filament\Pages\Auth\ConectaRegister;
use Filament\Navigation\NavigationGroup;
use Leandrocfe\FilamentApexCharts\FilamentApexChartsPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->brandName("HCL Licitações")
            ->id('admin')
            ->path('admin')
            ->login()
            ->registration(ConectaRegister::class)
            ->passwordReset()
            ->colors([
                'primary' => Color::Teal,
            ])
            ->sidebarCollapsibleOnDesktop()
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('Fundação')
                    ->icon('heroicon-o-user-group')
                    ->collapsed(true), // opcional: mantém expandido
                NavigationGroup::make()
                    ->label('Inteligência')
                    ->icon('heroicon-o-light-bulb')
                    ->collapsed(true), // opcional: mantém expandido,
                NavigationGroup::make()
                    ->label('Execução')
                    ->icon('heroicon-o-arrow-path')
                    ->collapsed(true), // opcional: mantém expandido,
                NavigationGroup::make()
                    ->label('Extras')
                    ->icon('heroicon-o-puzzle-piece')
                    ->collapsed(true), // opcional: mantém expandido,
                NavigationGroup::make()
                    ->label('Configurações')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->collapsed(true) // opcional: mantém expandido,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                //Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugins([
                FilamentApexChartsPlugin::make()
            ])
            ->databaseNotifications();
    }
}
