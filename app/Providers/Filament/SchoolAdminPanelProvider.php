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

                // ── Académique · Moteur canonique SPR-002 ──
                \App\Domains\Academic\Presentation\Filament\Resources\AcademicCalendarYearResource::class,
                \App\Domains\Academic\Presentation\Filament\Resources\AcademicCalendarTermResource::class,
                \App\Domains\Academic\Presentation\Filament\Resources\AcademicCalendarEventResource::class,
                \App\Domains\Academic\Presentation\Filament\Resources\EducationLevelResource::class,
                \App\Domains\Academic\Presentation\Filament\Resources\AcademicProgramResource::class,
                \App\Domains\Academic\Presentation\Filament\Resources\StreamResource::class,
                \App\Domains\Academic\Presentation\Filament\Resources\SchoolClassResource::class,
                \App\Domains\Academic\Presentation\Filament\Resources\ClassSectionResource::class,
                \App\Domains\Academic\Presentation\Filament\Resources\SubjectGroupResource::class,
                \App\Domains\Academic\Presentation\Filament\Resources\AcademicSubjectResource::class,
                \App\Domains\Academic\Presentation\Filament\Resources\CoefficientResource::class,

                // ── Utilisateurs ──
                \App\Filament\Resources\UserResource::class,
                \App\Filament\Resources\DepartmentResource::class,

                // ── Élèves ──
                \App\Domains\Student\Presentation\Filament\Resources\StudentResource::class,
                \App\Filament\Resources\StudentEnrollmentResource::class,

                // ── Tuteurs · Moteur canonique SPR-005 ──
                \App\Domains\Guardian\Presentation\Filament\Resources\GuardianResource::class,
                \App\Domains\Guardian\Presentation\Filament\Resources\StudentGuardianRelationshipResource::class,
                \App\Domains\Guardian\Presentation\Filament\Resources\EmergencyContactsResource::class,
                \App\Domains\Guardian\Presentation\Filament\Resources\AuthorizedPickupResource::class,

                // ── Personnel · Moteur canonique SPR-006 ──
                \App\Domains\Staff\Presentation\Filament\Resources\StaffResource::class,
                \App\Domains\Staff\Presentation\Filament\Resources\PositionResource::class,
                \App\Domains\Staff\Presentation\Filament\Resources\EmploymentResource::class,
                \App\Domains\Staff\Presentation\Filament\Resources\AssignmentResource::class,
                \App\Domains\Staff\Presentation\Filament\Resources\QualificationResource::class,
                \App\Domains\Staff\Presentation\Filament\Resources\CertificationResource::class,

                // ── Parcours scolaire · Moteur canonique SPR-004 ──
                \App\Domains\Enrollment\Presentation\Filament\Resources\EnrollmentResource::class,
                \App\Domains\Enrollment\Presentation\Filament\Resources\EnrollmentHistoryResource::class,
                \App\Domains\Enrollment\Presentation\Filament\Resources\PromotionResource::class,
                \App\Domains\Enrollment\Presentation\Filament\Resources\TransferResource::class,
                \App\Domains\Enrollment\Presentation\Filament\Resources\GraduationResource::class,
                \App\Domains\Enrollment\Presentation\Filament\Resources\BulkEnrollmentResource::class,

                // ── Présences · Moteur canonique SPR-008 ──
                \App\Domains\Attendance\Presentation\Filament\Resources\AttendanceSessionResource::class,
                \App\Domains\Attendance\Presentation\Filament\Resources\AttendanceRecordResource::class,
                \App\Domains\Attendance\Presentation\Filament\Resources\AttendanceCorrectionResource::class,
                \App\Domains\Attendance\Presentation\Filament\Resources\AttendanceAlertResource::class,
                \App\Domains\Attendance\Presentation\Filament\Resources\AttendanceSummaryResource::class,

                // ── Évaluations · Moteur canonique SPR-009 ──
                \App\Domains\Assessment\Presentation\Filament\Resources\AssessmentResource::class,
                \App\Domains\Assessment\Presentation\Filament\Resources\AssessmentTypeResource::class,
                \App\Domains\Assessment\Presentation\Filament\Resources\AssessmentSessionResource::class,
                \App\Domains\Assessment\Presentation\Filament\Resources\AssessmentScoreResource::class,
                \App\Domains\Assessment\Presentation\Filament\Resources\AssessmentTemplateResource::class,
                \App\Domains\Assessment\Presentation\Filament\Resources\AssessmentStatisticsResource::class,

                // ── Carnets de notes · Moteur canonique SPR-010 ──
                \App\Domains\Gradebook\Presentation\Filament\Resources\GradebookResource::class,
                \App\Domains\Gradebook\Presentation\Filament\Resources\GradeEntryResource::class,

                // ── Conseil de classe · Moteur canonique SPR-013 ──
                \App\Domains\ClassCouncil\Presentation\Filament\Resources\CouncilSessionResource::class,

                // ── Bulletins · Moteur canonique SPR-011 ──
                \App\Domains\ReportCard\Presentation\Filament\Resources\ReportCardResource::class,

                // ── Communication académique · Moteur canonique SPR-012 ──
                \App\Domains\AcademicCommunication\Presentation\Filament\Resources\AcademicDocumentResource::class,
                \App\Domains\AcademicCommunication\Presentation\Filament\Resources\AcademicDocumentTemplateResource::class,
                \App\Domains\AcademicCommunication\Presentation\Filament\Resources\PublicationPolicyResource::class,
                \App\Domains\AcademicCommunication\Presentation\Filament\Resources\PublicationChannelResource::class,

                // ── Politiques académiques · Moteur canonique SPR-009.5 ──
                \App\Domains\AcademicPolicy\Presentation\Filament\Resources\AcademicPolicyResource::class,

                // ── Vérification de documents · Moteur canonique SPR-016 ──
                \App\Domains\IdentityAndVerification\Presentation\Filament\Resources\VerificationIdentityResource::class,

                // ── Ressources déplacées (masquées, pour futurs panels) ──
                // Director: Incident, Report
                // Secretary: Message, Announcement
                // Accountant: Payment
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
                \App\Domains\Academic\Presentation\Filament\Widgets\CalendarOverviewWidget::class,
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
