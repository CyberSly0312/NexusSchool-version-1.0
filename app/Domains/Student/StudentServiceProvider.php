<?php

declare(strict_types=1);

namespace App\Domains\Student;

use App\Domains\Student\Application\Services\StudentNumberGenerator;
use App\Domains\Student\Application\Services\StudentService;
use App\Domains\Student\Domain\Events\StudentArchived;
use App\Domains\Student\Domain\Events\StudentCreated;
use App\Domains\Student\Domain\Events\StudentGraduated;
use App\Domains\Student\Domain\Events\StudentReactivated;
use App\Domains\Student\Domain\Events\StudentRestored;
use App\Domains\Student\Domain\Events\StudentSuspended;
use App\Domains\Student\Domain\Events\StudentTransferred;
use App\Domains\Student\Domain\Events\StudentUpdated;
use App\Domains\Student\Domain\Events\StudentWithdrawn;
use App\Domains\Student\Infrastructure\Listeners\StudentAuditLogger;
use App\Domains\Student\Infrastructure\Models\Student;
use App\Domains\Student\Infrastructure\Models\StudentGuardian;
use App\Domains\Student\Infrastructure\Observers\StudentEntityObserver;
use App\Domains\Student\Infrastructure\Policies\StudentPolicy;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

/**
 * Owning service provider for the Student domain (SPR-003). Registers the
 * domain's bindings, observers, policies and event listeners; follows the
 * SPR-002 decision D4 of keeping the legacy EventServiceProvider untouched.
 */
final class StudentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(StudentNumberGenerator::class);
        $this->app->singleton(StudentService::class);
    }

    public function boot(): void
    {
        $this->registerObservers();
        $this->registerPolicies();
        $this->registerEvents();
    }

    private function registerObservers(): void
    {
        $observer = StudentEntityObserver::class;

        Student::observe($observer);
        StudentGuardian::observe($observer);
    }

    private function registerPolicies(): void
    {
        Gate::policy(Student::class, StudentPolicy::class);
    }

    private function registerEvents(): void
    {
        Event::listen(StudentCreated::class, [StudentAuditLogger::class, 'handleStudentCreated']);
        Event::listen(StudentUpdated::class, [StudentAuditLogger::class, 'handleStudentUpdated']);
        Event::listen(StudentTransferred::class, [StudentAuditLogger::class, 'handleStudentTransferred']);
        Event::listen(StudentGraduated::class, [StudentAuditLogger::class, 'handleStudentGraduated']);
        Event::listen(StudentSuspended::class, [StudentAuditLogger::class, 'handleStudentSuspended']);
        Event::listen(StudentReactivated::class, [StudentAuditLogger::class, 'handleStudentReactivated']);
        Event::listen(StudentWithdrawn::class, [StudentAuditLogger::class, 'handleStudentWithdrawn']);
        Event::listen(StudentArchived::class, [StudentAuditLogger::class, 'handleStudentArchived']);
        Event::listen(StudentRestored::class, [StudentAuditLogger::class, 'handleStudentRestored']);
    }
}
