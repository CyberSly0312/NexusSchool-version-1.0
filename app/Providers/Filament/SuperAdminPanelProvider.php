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

class SuperAdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('super-admin')
            ->path('super-admin')
            ->login()
            ->brandName('NexusSchool — SaaS')
            ->colors([
                'primary' => Color::hex('#1e40af'),
            ])
            ->favicon(null)
            ->sidebarCollapsibleOnDesktop()
            ->pages([
                \App\Filament\SuperAdmin\Pages\SuperAdminDashboard::class,
            ])
            ->resources([
                // Plateforme
                \App\Filament\Resources\SuperAdminSchoolResource::class,
                \App\Filament\Resources\PlanResource::class,
                \App\Filament\Resources\SubscriptionResource::class,
                \App\Filament\Resources\DomainResource::class,
                \App\Filament\Resources\CentralUserResource::class,
                // Administration système
                \App\Filament\Resources\AuditLogResource::class,
                \App\Filament\Resources\SystemSettingResource::class,
                \App\Filament\Resources\BackupResource::class,
                \App\Filament\Resources\SystemHealthResource::class,
                \App\Filament\Resources\MaintenanceWindowResource::class,
                \App\Filament\Resources\NotificationChannelResource::class,
            ])
            ->widgets([
                \App\Filament\SuperAdmin\Widgets\StatsOverview::class,
                \App\Filament\SuperAdmin\Widgets\SchoolsChart::class,
                \App\Filament\SuperAdmin\Widgets\RevenueChart::class,
                \App\Filament\SuperAdmin\Widgets\SubscriptionStatusChart::class,
            ])
            ->middleware([
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
