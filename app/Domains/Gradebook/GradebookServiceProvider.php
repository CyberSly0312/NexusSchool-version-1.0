<?php

declare(strict_types=1);

namespace App\Domains\Gradebook;

use App\Domains\Gradebook\Application\Actions\ArchiveGradebookAction;
use App\Domains\Gradebook\Application\Actions\CalculateGradebookAction;
use App\Domains\Gradebook\Application\Actions\CreateGradebookAction;
use App\Domains\Gradebook\Application\Actions\LockGradebookAction;
use App\Domains\Gradebook\Application\Actions\PublishGradebookAction;
use App\Domains\Gradebook\Application\Actions\UnlockGradebookAction;
use App\Domains\Gradebook\Application\GradebookService;
use App\Domains\Gradebook\Application\Services\AcademicStandingService;
use App\Domains\Gradebook\Application\Services\GradeCalculationService;
use App\Domains\Gradebook\Application\Services\GradeRankingService;
use App\Domains\Gradebook\Domain\Contracts\AcademicFrameworkContract;
use App\Domains\Gradebook\Domain\Contracts\AssessmentContract;
use App\Domains\Gradebook\Domain\Contracts\EnrollmentContract;
use App\Domains\Gradebook\Domain\Contracts\GradebookContract;
use App\Domains\Gradebook\Domain\Events\GradebookArchived;
use App\Domains\Gradebook\Domain\Events\GradebookCalculated;
use App\Domains\Gradebook\Domain\Events\GradebookCreated;
use App\Domains\Gradebook\Domain\Events\GradebookLocked;
use App\Domains\Gradebook\Domain\Events\GradebookPublished;
use App\Domains\Gradebook\Domain\Events\GradebookUnlocked;
use App\Domains\Gradebook\Domain\Events\RankingsCalculated;
use App\Domains\Gradebook\Infrastructure\Contracts\AcademicFrameworkAdapter;
use App\Domains\Gradebook\Infrastructure\Contracts\AssessmentAdapter;
use App\Domains\Gradebook\Infrastructure\Contracts\EnrollmentAdapter;
use App\Domains\Gradebook\Infrastructure\Contracts\GradebookQueryAdapter;
use App\Domains\Gradebook\Infrastructure\Listeners\GradebookAuditLogger;
use App\Domains\Gradebook\Infrastructure\Models\Gradebook;
use App\Domains\Gradebook\Infrastructure\Models\GradeEntry;
use App\Domains\Gradebook\Infrastructure\Observers\GradebookEntityObserver;
use App\Domains\Gradebook\Infrastructure\Policies\GradebookPolicy;
use App\Domains\Gradebook\Infrastructure\Policies\GradeEntryPolicy;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

/**
 * Owning service provider for the Gradebook engine (SPR-010). Registers the
 * cross-domain contract bindings (adapters — the only place the Gradebook
 * domain sees foreign models), the application services and actions, observers,
 * policies and event listeners. Follows the SPR-009 Assessment precedent.
 */
final class GradebookServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../../config/nexus-gradebook.php',
            'nexus-gradebook',
        );

        $this->app->singleton(GradebookService::class);
        $this->app->singleton(GradeCalculationService::class);
        $this->app->singleton(GradeRankingService::class);
        $this->app->singleton(AcademicStandingService::class);

        $this->app->bind(CreateGradebookAction::class);
        $this->app->bind(CalculateGradebookAction::class);
        $this->app->bind(LockGradebookAction::class);
        $this->app->bind(UnlockGradebookAction::class);
        $this->app->bind(PublishGradebookAction::class);
        $this->app->bind(ArchiveGradebookAction::class);

        $this->app->bind(AcademicFrameworkContract::class, AcademicFrameworkAdapter::class);
        $this->app->bind(EnrollmentContract::class, EnrollmentAdapter::class);
        $this->app->bind(AssessmentContract::class, AssessmentAdapter::class);
        $this->app->bind(GradebookContract::class, GradebookQueryAdapter::class);
    }

    public function boot(): void
    {
        $this->registerObservers();
        $this->registerPolicies();
        $this->registerEvents();
    }

    private function registerObservers(): void
    {
        $observer = GradebookEntityObserver::class;

        Gradebook::observe($observer);
        GradeEntry::observe($observer);
    }

    private function registerPolicies(): void
    {
        Gate::policy(Gradebook::class, GradebookPolicy::class);
        Gate::policy(GradeEntry::class, GradeEntryPolicy::class);
    }

    private function registerEvents(): void
    {
        Event::listen(GradebookCreated::class, [GradebookAuditLogger::class, 'handleCreated']);
        Event::listen(GradebookCalculated::class, [GradebookAuditLogger::class, 'handleCalculated']);
        Event::listen(RankingsCalculated::class, [GradebookAuditLogger::class, 'handleRankingsCalculated']);
        Event::listen(GradebookLocked::class, [GradebookAuditLogger::class, 'handleLocked']);
        Event::listen(GradebookUnlocked::class, [GradebookAuditLogger::class, 'handleUnlocked']);
        Event::listen(GradebookPublished::class, [GradebookAuditLogger::class, 'handlePublished']);
        Event::listen(GradebookArchived::class, [GradebookAuditLogger::class, 'handleArchived']);
    }
}
