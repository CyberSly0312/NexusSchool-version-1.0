<?php

declare(strict_types=1);

namespace App\Domains\Enrollment;

use App\Domains\Enrollment\Application\Services\EnrollmentNumberGenerator;
use App\Domains\Enrollment\Application\Services\EnrollmentService;
use App\Domains\Enrollment\Domain\Events\EnrollmentCreated;
use App\Domains\Enrollment\Domain\Events\EnrollmentRestored;
use App\Domains\Enrollment\Domain\Events\EnrollmentSuspended;
use App\Domains\Enrollment\Domain\Events\EnrollmentUpdated;
use App\Domains\Enrollment\Domain\Events\StudentGraduated;
use App\Domains\Enrollment\Domain\Events\StudentPromoted;
use App\Domains\Enrollment\Domain\Events\StudentRepeated;
use App\Domains\Enrollment\Domain\Events\StudentTransferred;
use App\Domains\Enrollment\Domain\Events\StudentWithdrawn;
use App\Domains\Enrollment\Infrastructure\Listeners\EnrollmentAuditLogger;
use App\Domains\Enrollment\Infrastructure\Models\Enrollment;
use App\Domains\Enrollment\Infrastructure\Models\EnrollmentGraduation;
use App\Domains\Enrollment\Infrastructure\Models\EnrollmentHistory;
use App\Domains\Enrollment\Infrastructure\Models\EnrollmentPromotion;
use App\Domains\Enrollment\Infrastructure\Models\EnrollmentTransfer;
use App\Domains\Enrollment\Infrastructure\Observers\EnrollmentEntityObserver;
use App\Domains\Enrollment\Infrastructure\Policies\EnrollmentPolicy;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

/**
 * Owning service provider for the Enrollment domain (SPR-004). Registers the
 * domain's bindings, observers, policies and event listeners; follows the
 * SPR-002 decision D4 of keeping the legacy EventServiceProvider untouched.
 */
final class EnrollmentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(EnrollmentNumberGenerator::class);
        $this->app->singleton(EnrollmentService::class);
    }

    public function boot(): void
    {
        $this->registerObservers();
        $this->registerPolicies();
        $this->registerEvents();
    }

    private function registerObservers(): void
    {
        $observer = EnrollmentEntityObserver::class;

        Enrollment::observe($observer);
        EnrollmentPromotion::observe($observer);
        EnrollmentTransfer::observe($observer);
        EnrollmentGraduation::observe($observer);
        EnrollmentHistory::observe($observer);
    }

    private function registerPolicies(): void
    {
        Gate::policy(Enrollment::class, EnrollmentPolicy::class);
    }

    private function registerEvents(): void
    {
        Event::listen(EnrollmentCreated::class, [EnrollmentAuditLogger::class, 'handleEnrollmentCreated']);
        Event::listen(EnrollmentUpdated::class, [EnrollmentAuditLogger::class, 'handleEnrollmentUpdated']);
        Event::listen(StudentPromoted::class, [EnrollmentAuditLogger::class, 'handleStudentPromoted']);
        Event::listen(StudentRepeated::class, [EnrollmentAuditLogger::class, 'handleStudentRepeated']);
        Event::listen(StudentTransferred::class, [EnrollmentAuditLogger::class, 'handleStudentTransferred']);
        Event::listen(StudentGraduated::class, [EnrollmentAuditLogger::class, 'handleStudentGraduated']);
        Event::listen(StudentWithdrawn::class, [EnrollmentAuditLogger::class, 'handleStudentWithdrawn']);
        Event::listen(EnrollmentSuspended::class, [EnrollmentAuditLogger::class, 'handleEnrollmentSuspended']);
        Event::listen(EnrollmentRestored::class, [EnrollmentAuditLogger::class, 'handleEnrollmentRestored']);
    }
}
