<?php

declare(strict_types=1);

namespace App\Domains\Attendance;

use App\Domains\Attendance\Application\Actions\BulkAttendanceAction;
use App\Domains\Attendance\Application\Actions\CalculateAttendanceStatisticsAction;
use App\Domains\Attendance\Application\Actions\CorrectAttendanceAction;
use App\Domains\Attendance\Application\Actions\GenerateAttendanceSessionAction;
use App\Domains\Attendance\Application\Actions\GenerateAttendanceSummaryAction;
use App\Domains\Attendance\Application\Actions\LockAttendanceSessionAction;
use App\Domains\Attendance\Application\Actions\OpenAttendanceSessionAction;
use App\Domains\Attendance\Application\Actions\RecordAttendanceAction;
use App\Domains\Attendance\Application\Actions\UnlockAttendanceSessionAction;
use App\Domains\Attendance\Application\Services\AttendanceAlertDispatcher;
use App\Domains\Attendance\Application\Services\AttendanceRecordQuery;
use App\Domains\Attendance\Application\Services\AttendanceRiskEngine;
use App\Domains\Attendance\Application\Services\AttendanceService;
use App\Domains\Attendance\Application\Services\AttendanceStatisticsCalculator;
use App\Domains\Attendance\Domain\Contracts\AcademicFrameworkContract;
use App\Domains\Attendance\Domain\Contracts\AnalyticsContract;
use App\Domains\Attendance\Domain\Contracts\EnrollmentContract;
use App\Domains\Attendance\Domain\Contracts\FutureNotificationContract;
use App\Domains\Attendance\Domain\Contracts\StaffContract;
use App\Domains\Attendance\Domain\Contracts\StudentIdentityContract;
use App\Domains\Attendance\Domain\Contracts\TimetableContract;
use App\Domains\Attendance\Domain\Events\AttendanceAlertGenerated;
use App\Domains\Attendance\Domain\Events\AttendanceCorrected;
use App\Domains\Attendance\Domain\Events\AttendanceLocked;
use App\Domains\Attendance\Domain\Events\AttendanceRecorded;
use App\Domains\Attendance\Domain\Events\AttendanceSessionCreated;
use App\Domains\Attendance\Domain\Events\AttendanceSessionOpened;
use App\Domains\Attendance\Domain\Events\AttendanceUnlocked;
use App\Domains\Attendance\Infrastructure\Contracts\AcademicFrameworkAdapter;
use App\Domains\Attendance\Infrastructure\Contracts\AnalyticsAdapter;
use App\Domains\Attendance\Infrastructure\Contracts\EnrollmentAdapter;
use App\Domains\Attendance\Infrastructure\Contracts\FutureNotificationAdapter;
use App\Domains\Attendance\Infrastructure\Contracts\StaffAdapter;
use App\Domains\Attendance\Infrastructure\Contracts\StudentIdentityAdapter;
use App\Domains\Attendance\Infrastructure\Contracts\TimetableAdapter;
use App\Domains\Attendance\Infrastructure\Listeners\AttendanceAuditLogger;
use App\Domains\Attendance\Infrastructure\Models\AttendanceAlert;
use App\Domains\Attendance\Infrastructure\Models\AttendanceCorrection;
use App\Domains\Attendance\Infrastructure\Models\AttendanceRecord;
use App\Domains\Attendance\Infrastructure\Models\AttendanceSession;
use App\Domains\Attendance\Infrastructure\Models\AttendanceSummary;
use App\Domains\Attendance\Infrastructure\Models\EarlyDeparture;
use App\Domains\Attendance\Infrastructure\Models\LateArrival;
use App\Domains\Attendance\Infrastructure\Observers\AttendanceEntityObserver;
use App\Domains\Attendance\Infrastructure\Policies\AttendanceAlertPolicy;
use App\Domains\Attendance\Infrastructure\Policies\AttendanceCorrectionPolicy;
use App\Domains\Attendance\Infrastructure\Policies\AttendanceRecordPolicy;
use App\Domains\Attendance\Infrastructure\Policies\AttendanceSessionPolicy;
use App\Domains\Attendance\Infrastructure\Policies\AttendanceSummaryPolicy;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

/**
 * Owning service provider for the Attendance engine (SPR-008). Registers the
 * cross-domain contract bindings (adapters — the only place the Attendance
 * domain sees foreign models), the application services, observers, policies
 * and event listeners. Follows the SPR-007 Timetable precedent.
 */
final class AttendanceServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../../config/nexus-attendance.php',
            'nexus-attendance',
        );

        $this->app->singleton(AttendanceService::class);
        $this->app->singleton(AttendanceStatisticsCalculator::class);
        $this->app->singleton(AttendanceRiskEngine::class);
        $this->app->singleton(AttendanceRecordQuery::class);
        $this->app->singleton(AttendanceAlertDispatcher::class);

        $this->app->bind(GenerateAttendanceSessionAction::class);
        $this->app->bind(OpenAttendanceSessionAction::class);
        $this->app->bind(RecordAttendanceAction::class);
        $this->app->bind(BulkAttendanceAction::class);
        $this->app->bind(CorrectAttendanceAction::class);
        $this->app->bind(LockAttendanceSessionAction::class);
        $this->app->bind(UnlockAttendanceSessionAction::class);
        $this->app->bind(CalculateAttendanceStatisticsAction::class);
        $this->app->bind(GenerateAttendanceSummaryAction::class);

        $this->app->bind(AcademicFrameworkContract::class, AcademicFrameworkAdapter::class);
        $this->app->bind(StudentIdentityContract::class, StudentIdentityAdapter::class);
        $this->app->bind(EnrollmentContract::class, EnrollmentAdapter::class);
        $this->app->bind(StaffContract::class, StaffAdapter::class);
        $this->app->bind(TimetableContract::class, TimetableAdapter::class);
        $this->app->bind(FutureNotificationContract::class, FutureNotificationAdapter::class);
        $this->app->bind(AnalyticsContract::class, AnalyticsAdapter::class);
    }

    public function boot(): void
    {
        $this->registerObservers();
        $this->registerPolicies();
        $this->registerEvents();
    }

    private function registerObservers(): void
    {
        $observer = AttendanceEntityObserver::class;

        AttendanceSession::observe($observer);
        AttendanceRecord::observe($observer);
        AttendanceCorrection::observe($observer);
        AttendanceAlert::observe($observer);
        AttendanceSummary::observe($observer);
        LateArrival::observe($observer);
        EarlyDeparture::observe($observer);
    }

    private function registerPolicies(): void
    {
        Gate::policy(AttendanceSession::class, AttendanceSessionPolicy::class);
        Gate::policy(AttendanceRecord::class, AttendanceRecordPolicy::class);
        Gate::policy(AttendanceCorrection::class, AttendanceCorrectionPolicy::class);
        Gate::policy(AttendanceAlert::class, AttendanceAlertPolicy::class);
        Gate::policy(AttendanceSummary::class, AttendanceSummaryPolicy::class);
    }

    private function registerEvents(): void
    {
        Event::listen(AttendanceSessionCreated::class, [AttendanceAuditLogger::class, 'handleSessionCreated']);
        Event::listen(AttendanceSessionOpened::class, [AttendanceAuditLogger::class, 'handleSessionOpened']);
        Event::listen(AttendanceRecorded::class, [AttendanceAuditLogger::class, 'handleRecorded']);
        Event::listen(AttendanceCorrected::class, [AttendanceAuditLogger::class, 'handleCorrected']);
        Event::listen(AttendanceLocked::class, [AttendanceAuditLogger::class, 'handleLocked']);
        Event::listen(AttendanceUnlocked::class, [AttendanceAuditLogger::class, 'handleUnlocked']);
        Event::listen(AttendanceAlertGenerated::class, [AttendanceAuditLogger::class, 'handleAlertGenerated']);
    }
}
