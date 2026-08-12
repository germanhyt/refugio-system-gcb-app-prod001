<?php

namespace App\Providers\Filament;

use App\Models\SiteSetting;
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

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName('Panel Refugio')
            ->brandLogo(fn () => view('filament.admin-brand', [
                'logoUrl' => $this->brandLogoUrl(),
            ]))
            ->brandLogoHeight('2.75rem')
            ->favicon(fn (): string => $this->faviconUrl())
            ->colors([
                'primary' => Color::hex('#A7623D'),
            ])
            ->navigationGroups([
                'Contenido',
                'Operaciones',
                'Configuración',
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
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
            ]);
    }

    private function brandLogoUrl(): string
    {
        try {
            $logo = SiteSetting::current()->getFirstMediaUrl('logo');
            if (filled($logo)) {
                return $logo;
            }
        } catch (\Throwable) {
            // DB may be unavailable during early boot / artisan package discovery.
        }

        return asset('images/refugio/logo-v2.svg');
    }

    private function faviconUrl(): string
    {
        try {
            $favicon = SiteSetting::current()->getFirstMediaUrl('favicon');
            if (filled($favicon)) {
                return $favicon;
            }
        } catch (\Throwable) {
            // ignore
        }

        return asset('images/refugio/favicon-150x150.png');
    }
}
