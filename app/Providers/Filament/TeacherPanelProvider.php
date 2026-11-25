<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class TeacherPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('teacher')
            ->path('teacher')
            ->login()
            ->brandName('NexusSchool')
            ->colors([
                'primary' => Color::hex('#059669'),
            ])
            ->sidebarCollapsibleOnDesktop()
            ->pages([
                \App\Filament\Teacher\Pages\TeacherDashboard::class,
                \App\Filament\Pages\ChangePassword::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                \Filament\Http\Middleware\AuthenticateSession::class,
                ShareErrorsFromSession::class,
                \App\Http\Middleware\InitializeTenancyForPanel::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                \Filament\Http\Middleware\DisableBladeIconComponents::class,
                \Filament\Http\Middleware\DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                \Filament\Http\Middleware\Authenticate::class,
            ]);
    }
}
