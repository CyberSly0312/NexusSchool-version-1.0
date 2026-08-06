<?php

declare(strict_types=1);

namespace App\Domains\Staff;

use App\Domains\Staff\Application\Services\StaffNumberGenerator;
use App\Domains\Staff\Application\Services\StaffService;
use App\Domains\Staff\Domain\Events\DepartmentAssigned;
use App\Domains\Staff\Domain\Events\EmploymentStarted;
use App\Domains\Staff\Domain\Events\EmploymentTerminated;
use App\Domains\Staff\Domain\Events\EmploymentTransferred;
use App\Domains\Staff\Domain\Events\PositionAssigned;
use App\Domains\Staff\Domain\Events\StaffCreated;
use App\Domains\Staff\Domain\Events\StaffRestored;
use App\Domains\Staff\Domain\Events\StaffSuspended;
use App\Domains\Staff\Domain\Events\StaffUpdated;
use App\Domains\Staff\Domain\Events\TeacherSubjectsAssigned;
use App\Domains\Staff\Infrastructure\Listeners\StaffAuditLogger;
use App\Domains\Staff\Infrastructure\Models\Assignment;
use App\Domains\Staff\Infrastructure\Models\Certification;
use App\Domains\Staff\Infrastructure\Models\Employment;
use App\Domains\Staff\Infrastructure\Models\EmploymentHistory;
use App\Domains\Staff\Infrastructure\Models\Position;
use App\Domains\Staff\Infrastructure\Models\Qualification;
use App\Domains\Staff\Infrastructure\Models\Staff;
use App\Domains\Staff\Infrastructure\Models\StaffEmergencyContact;
use App\Domains\Staff\Infrastructure\Models\TeacherProfile;
use App\Domains\Staff\Infrastructure\Observers\StaffEntityObserver;
use App\Domains\Staff\Infrastructure\Policies\StaffPolicy;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

/**
 * Owning service provider for the Staff & Workforce Engine (SPR-006). Registers
 * the domain's bindings, observers, policies and event listeners; follows the
 * SPR-002 decision D4 of keeping the legacy EventServiceProvider untouched.
 */
final class StaffServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(StaffNumberGenerator::class);
        $this->app->singleton(StaffService::class);
    }

    public function boot(): void
    {
        $this->registerObservers();
        $this->registerPolicies();
        $this->registerEvents();
    }

    private function registerObservers(): void
    {
        $observer = StaffEntityObserver::class;

        Staff::observe($observer);
        Employment::observe($observer);
        EmploymentHistory::observe($observer);
        Position::observe($observer);
        Assignment::observe($observer);
        Qualification::observe($observer);
        Certification::observe($observer);
        StaffEmergencyContact::observe($observer);
        TeacherProfile::observe($observer);
    }

    private function registerPolicies(): void
    {
        Gate::policy(Staff::class, StaffPolicy::class);
    }

    private function registerEvents(): void
    {
        Event::listen(StaffCreated::class, [StaffAuditLogger::class, 'handleStaffCreated']);
        Event::listen(StaffUpdated::class, [StaffAuditLogger::class, 'handleStaffUpdated']);
        Event::listen(StaffSuspended::class, [StaffAuditLogger::class, 'handleStaffSuspended']);
        Event::listen(StaffRestored::class, [StaffAuditLogger::class, 'handleStaffRestored']);
        Event::listen(EmploymentStarted::class, [StaffAuditLogger::class, 'handleEmploymentStarted']);
        Event::listen(EmploymentTerminated::class, [StaffAuditLogger::class, 'handleEmploymentTerminated']);
        Event::listen(EmploymentTransferred::class, [StaffAuditLogger::class, 'handleEmploymentTransferred']);
        Event::listen(DepartmentAssigned::class, [StaffAuditLogger::class, 'handleDepartmentAssigned']);
        Event::listen(PositionAssigned::class, [StaffAuditLogger::class, 'handlePositionAssigned']);
        Event::listen(TeacherSubjectsAssigned::class, [StaffAuditLogger::class, 'handleTeacherSubjectsAssigned']);
    }
}
