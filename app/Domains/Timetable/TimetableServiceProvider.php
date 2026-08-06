<?php

declare(strict_types=1);

namespace App\Domains\Timetable;

use App\Domains\Timetable\Application\Services\ScheduleEntryValidator;
use App\Domains\Timetable\Application\Services\TimetableConflictEngine;
use App\Domains\Timetable\Application\Services\TimetableQueryService;
use App\Domains\Timetable\Application\Services\TimetableService;
use App\Domains\Timetable\Domain\Contracts\AcademicFrameworkContract;
use App\Domains\Timetable\Domain\Contracts\AssessmentContract;
use App\Domains\Timetable\Domain\Contracts\EnrollmentContract;
use App\Domains\Timetable\Domain\Contracts\RoomContract;
use App\Domains\Timetable\Domain\Contracts\StaffContract;
use App\Domains\Timetable\Domain\Contracts\StudentIdentityContract;
use App\Domains\Timetable\Domain\Events\AvailabilityConstraintCreated;
use App\Domains\Timetable\Domain\Events\ConflictDetected;
use App\Domains\Timetable\Domain\Events\ConflictResolved;
use App\Domains\Timetable\Domain\Events\RoomAssigned;
use App\Domains\Timetable\Domain\Events\ScheduleEntryCreated;
use App\Domains\Timetable\Domain\Events\ScheduleEntryUpdated;
use App\Domains\Timetable\Domain\Events\TeacherAssigned;
use App\Domains\Timetable\Domain\Events\TimetableArchived;
use App\Domains\Timetable\Domain\Events\TimetableCreated;
use App\Domains\Timetable\Domain\Events\TimetableDuplicated;
use App\Domains\Timetable\Domain\Events\TimetablePublished;
use App\Domains\Timetable\Infrastructure\Contracts\AcademicFrameworkAdapter;
use App\Domains\Timetable\Infrastructure\Contracts\AssessmentAdapter;
use App\Domains\Timetable\Infrastructure\Contracts\EnrollmentAdapter;
use App\Domains\Timetable\Infrastructure\Contracts\RoomAdapter;
use App\Domains\Timetable\Infrastructure\Contracts\StaffAdapter;
use App\Domains\Timetable\Infrastructure\Contracts\StudentIdentityAdapter;
use App\Domains\Timetable\Infrastructure\Listeners\TimetableAuditLogger;
use App\Domains\Timetable\Infrastructure\Models\AvailabilityConstraint;
use App\Domains\Timetable\Infrastructure\Models\RecurringPattern;
use App\Domains\Timetable\Infrastructure\Models\RoomAssignment;
use App\Domains\Timetable\Infrastructure\Models\ScheduleEntry;
use App\Domains\Timetable\Infrastructure\Models\TeachingPeriod;
use App\Domains\Timetable\Infrastructure\Models\TimeSlot;
use App\Domains\Timetable\Infrastructure\Models\Timetable;
use App\Domains\Timetable\Infrastructure\Models\TimetableConflict;
use App\Domains\Timetable\Infrastructure\Models\TimetableVersion;
use App\Domains\Timetable\Infrastructure\Observers\TimetableEntityObserver;
use App\Domains\Timetable\Infrastructure\Policies\ScheduleEntryPolicy;
use App\Domains\Timetable\Infrastructure\Policies\TimetableConflictPolicy;
use App\Domains\Timetable\Infrastructure\Policies\TimetablePolicy;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

/**
 * Owning service provider for the Timetable engine (SPR-007). Registers the
 * cross-domain contract bindings (adapters), domain services, observers,
 * policies and event listeners. Follows the SPR-002 D4 convention of keeping
 * the legacy EventServiceProvider untouched.
 */
final class TimetableServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(TimetableService::class);
        $this->app->singleton(TimetableQueryService::class);
        $this->app->singleton(TimetableConflictEngine::class);
        $this->app->singleton(ScheduleEntryValidator::class);

        $this->app->bind(AcademicFrameworkContract::class, AcademicFrameworkAdapter::class);
        $this->app->bind(StaffContract::class, StaffAdapter::class);
        $this->app->bind(StudentIdentityContract::class, StudentIdentityAdapter::class);
        $this->app->bind(EnrollmentContract::class, EnrollmentAdapter::class);
        $this->app->bind(RoomContract::class, RoomAdapter::class);
        $this->app->bind(AssessmentContract::class, AssessmentAdapter::class);
    }

    public function boot(): void
    {
        $this->registerObservers();
        $this->registerPolicies();
        $this->registerEvents();
    }

    private function registerObservers(): void
    {
        $observer = TimetableEntityObserver::class;

        Timetable::observe($observer);
        TimetableVersion::observe($observer);
        TeachingPeriod::observe($observer);
        TimeSlot::observe($observer);
        ScheduleEntry::observe($observer);
        RoomAssignment::observe($observer);
        RecurringPattern::observe($observer);
        AvailabilityConstraint::observe($observer);
        TimetableConflict::observe($observer);
    }

    private function registerPolicies(): void
    {
        Gate::policy(Timetable::class, TimetablePolicy::class);
        Gate::policy(ScheduleEntry::class, ScheduleEntryPolicy::class);
        Gate::policy(TimetableConflict::class, TimetableConflictPolicy::class);
    }

    private function registerEvents(): void
    {
        Event::listen(TimetableCreated::class, [TimetableAuditLogger::class, 'handleTimetableCreated']);
        Event::listen(TimetablePublished::class, [TimetableAuditLogger::class, 'handleTimetablePublished']);
        Event::listen(TimetableArchived::class, [TimetableAuditLogger::class, 'handleTimetableArchived']);
        Event::listen(TimetableDuplicated::class, [TimetableAuditLogger::class, 'handleTimetableDuplicated']);
        Event::listen(ScheduleEntryCreated::class, [TimetableAuditLogger::class, 'handleScheduleEntryCreated']);
        Event::listen(ScheduleEntryUpdated::class, [TimetableAuditLogger::class, 'handleScheduleEntryUpdated']);
        Event::listen(TeacherAssigned::class, [TimetableAuditLogger::class, 'handleTeacherAssigned']);
        Event::listen(RoomAssigned::class, [TimetableAuditLogger::class, 'handleRoomAssigned']);
        Event::listen(ConflictDetected::class, [TimetableAuditLogger::class, 'handleConflictDetected']);
        Event::listen(ConflictResolved::class, [TimetableAuditLogger::class, 'handleConflictResolved']);
        Event::listen(AvailabilityConstraintCreated::class, [TimetableAuditLogger::class, 'handleAvailabilityConstraintCreated']);
    }
}
