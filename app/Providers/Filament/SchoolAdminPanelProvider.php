<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class SchoolAdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('school-admin')
            ->path('school-admin')
            ->login()
            ->brandName('NexusSchool')
            ->colors([
                'primary' => Color::hex('#00a2ff'),
            ])
            ->sidebarCollapsibleOnDesktop()
            ->pages([
                \App\Filament\SchoolAdmin\Pages\SchoolAdminDashboard::class,
                \App\Filament\SchoolAdmin\Pages\EstablishmentInfo::class,
                \App\Filament\SchoolAdmin\Pages\PDFSettings::class,
                \App\Filament\SchoolAdmin\Pages\FeeConfiguration::class,
            ])
            ->resources([
                // ── Établissement ──
                \App\Filament\Resources\CampusResource::class,

                // ── Académique ──
                \App\Filament\Resources\AcademicYearResource::class,
                \App\Filament\Resources\AcademicTermResource::class,
                \App\Filament\Resources\ProgramResource::class,
                \App\Filament\Resources\GradeLevelResource::class,
                \App\Filament\Resources\SectionResource::class,
                \App\Filament\Resources\AcademicClassResource::class,
                \App\Filament\Resources\ClassroomResource::class,
                \App\Filament\Resources\SubjectCategoryResource::class,
                \App\Filament\Resources\SubjectResource::class,
                \App\Filament\Resources\SubjectCoefficientResource::class,

                // ── Utilisateurs ──
                \App\Filament\Resources\UserResource::class,
                \App\Filament\Resources\DepartmentResource::class,

                // ── Élèves ──
                \App\Filament\Resources\StudentResource::class,
                \App\Filament\Resources\GuardianResource::class,
                \App\Filament\Resources\StudentEnrollmentResource::class,

                // ── Ressources déplacées (masquées, pour futurs panels) ──
                // Director: AssessmentResult, Incident, Report
                // Secretary: Message, Announcement
                // Accountant: Payment
                \App\Filament\Resources\AssessmentResultResource::class,
                \App\Filament\Resources\IncidentResource::class,
                \App\Filament\Resources\ReportResource::class,
                \App\Filament\Resources\MessageResource::class,
                \App\Filament\Resources\AnnouncementResource::class,
                \App\Filament\Resources\PaymentResource::class,
            ])
            ->widgets([
                \App\Filament\SchoolAdmin\Widgets\ConfigStatusWidget::class,
                \App\Filament\SchoolAdmin\Widgets\ConfigAlertsWidget::class,
                \App\Filament\SchoolAdmin\Widgets\ActiveAcademicYearWidget::class,
                \App\Filament\SchoolAdmin\Widgets\StudentsCountWidget::class,
                \App\Filament\SchoolAdmin\Widgets\TeachersCountWidget::class,
                \App\Filament\SchoolAdmin\Widgets\StaffCountWidget::class,
                \App\Filament\SchoolAdmin\Widgets\ClassesCountWidget::class,
                \App\Filament\SchoolAdmin\Widgets\SubjectsCountWidget::class,
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
