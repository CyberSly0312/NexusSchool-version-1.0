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
