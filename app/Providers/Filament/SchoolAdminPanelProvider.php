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
                \App\Filament\SchoolAdmin\Pages\SchoolSettings::class,
                \App\Filament\SchoolAdmin\Pages\PDFSettings::class,
                \App\Filament\SchoolAdmin\Pages\FeeConfiguration::class,
                \App\Filament\SchoolAdmin\Pages\ImportData::class,
            ])
            ->resources([
                // Ecole — Campus
                \App\Filament\Resources\CampusResource::class,

                // Ecole — Eleves
                \App\Filament\Resources\StudentResource::class,
                \App\Filament\Resources\GuardianResource::class,
                \App\Filament\Resources\StudentEnrollmentResource::class,

                // Enseignements — Academic
                \App\Filament\Resources\AcademicYearResource::class,
                \App\Filament\Resources\AcademicTermResource::class,
                \App\Filament\Resources\AcademicClassResource::class,
                \App\Filament\Resources\ProgramResource::class,
                \App\Filament\Resources\GradeLevelResource::class,
                \App\Filament\Resources\SectionResource::class,
                \App\Filament\Resources\ClassroomResource::class,
                \App\Filament\Resources\SubjectResource::class,
                \App\Filament\Resources\SubjectCategoryResource::class,
                \App\Filament\Resources\SubjectCoefficientResource::class,

                // Examens
                \App\Filament\Resources\AssessmentResultResource::class,

                // Presences
                \App\Filament\Resources\AttendanceResource::class,

                // Finances
                \App\Filament\Resources\PaymentResource::class,

                // Communication
                \App\Filament\Resources\AnnouncementResource::class,
                \App\Filament\Resources\MessageResource::class,

                // Securite
                \App\Filament\Resources\IncidentResource::class,

                // Rapports
                \App\Filament\Resources\ReportResource::class,

                // Administration — Utilisateurs
                \App\Filament\Resources\UserResource::class,
                \App\Filament\Resources\DepartmentResource::class,
            ])
            ->widgets([
                \App\Filament\SchoolAdmin\Widgets\SetupStatusWidget::class,
                \App\Filament\SchoolAdmin\Widgets\ActiveAcademicYearWidget::class,
                \App\Filament\SchoolAdmin\Widgets\StudentsCountWidget::class,
                \App\Filament\SchoolAdmin\Widgets\TeachersCountWidget::class,
                \App\Filament\SchoolAdmin\Widgets\StaffCountWidget::class,
                \App\Filament\SchoolAdmin\Widgets\ClassesCountWidget::class,
                \App\Filament\SchoolAdmin\Widgets\SubjectsCountWidget::class,
                \App\Filament\SchoolAdmin\Widgets\ConfigurationAlertsWidget::class,
                \App\Filament\SchoolAdmin\Widgets\RecentEnrollmentsWidget::class,
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
