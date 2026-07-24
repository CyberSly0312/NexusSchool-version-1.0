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

class DirectorPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('director')
            ->path('director')
            ->login()
            ->brandName('NexusSchool')
            ->colors([
                'primary' => Color::hex('#00a2ff'),
            ])
            ->favicon(null)
            ->sidebarCollapsibleOnDesktop()
            ->pages([
                \App\Filament\Director\Pages\DirectorDashboard::class,
                \App\Filament\Director\Pages\DocumentCenter::class,
                \App\Filament\Director\Pages\SchoolIdentity::class,
                \App\Filament\Director\Pages\SchoolWizard::class,
            ])
            ->widgets([
                \App\Filament\Director\Widgets\DirectorStatsOverview::class,
                \App\Filament\Director\Widgets\EnrollmentChart::class,
                \App\Filament\Director\Widgets\AttendanceChart::class,
                \App\Filament\Director\Widgets\PaymentsChart::class,
                \App\Filament\Director\Widgets\AcademicResultsChart::class,
            ])
            ->middleware([
                \App\Http\Middleware\InitializeTenancyForPanel::class,
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                \Filament\Http\Middleware\AuthenticateSession::class,
                ShareErrorsFromSession::class,
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
