<?php

declare(strict_types=1);

use App\Providers\AppServiceProvider;
use App\Providers\TenancyServiceProvider;

return [
    AppServiceProvider::class,
    TenancyServiceProvider::class,
    \App\Domains\Plugin\PluginServiceProvider::class,
    \App\Domains\Academic\AcademicServiceProvider::class,
    \App\Domains\Student\StudentServiceProvider::class,
    \App\Domains\Enrollment\EnrollmentServiceProvider::class,
    \App\Domains\Guardian\GuardianServiceProvider::class,
    \App\Domains\Staff\StaffServiceProvider::class,
    \App\Domains\Timetable\TimetableServiceProvider::class,
    \App\Domains\Attendance\AttendanceServiceProvider::class,
    \App\Domains\Assessment\AssessmentServiceProvider::class,
    \App\Domains\Gradebook\GradebookServiceProvider::class,
    \App\Domains\ClassCouncil\ClassCouncilServiceProvider::class,
    \App\Domains\ReportCard\ReportCardServiceProvider::class,
    \App\Domains\CertificateAndTranscript\CertificateAndTranscriptServiceProvider::class,
    \App\Domains\AcademicCommunication\AcademicCommunicationServiceProvider::class,
    \App\Domains\AcademicPolicy\AcademicPolicyServiceProvider::class,
    \App\Domains\RecordsLifecycle\RecordsLifecycleServiceProvider::class,
    \App\Domains\IdentityAndVerification\IdentityAndVerificationServiceProvider::class,
    \App\Domains\User\UserServiceProvider::class,
    \App\Domains\Role\RoleServiceProvider::class,

    // Filament Panel Providers
    \App\Providers\Filament\SuperAdminPanelProvider::class,
    \App\Providers\Filament\SchoolAdminPanelProvider::class,
    \App\Providers\Filament\DirectorPanelProvider::class,
    \App\Providers\Filament\TeacherPanelProvider::class,
    \App\Providers\Filament\StudentPanelProvider::class,
    \App\Providers\Filament\ParentPanelProvider::class,
    \App\Providers\Filament\AccountantPanelProvider::class,
    \App\Providers\Filament\SecretaryPanelProvider::class,
];
