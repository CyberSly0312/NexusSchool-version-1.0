<?php

declare(strict_types=1);

namespace App\Domains\ClassCouncil;

use App\Domains\ClassCouncil\Application\Actions\ArchiveCouncilSessionAction;
use App\Domains\ClassCouncil\Application\Actions\ConveneCouncilSessionAction;
use App\Domains\ClassCouncil\Application\Actions\CreateCouncilSessionAction;
use App\Domains\ClassCouncil\Application\Actions\PublishCouncilSessionAction;
use App\Domains\ClassCouncil\Application\Actions\RecordCouncilDecisionAction;
use App\Domains\ClassCouncil\Application\Actions\RecordCouncilDeliberationAction;
use App\Domains\ClassCouncil\Application\ClassCouncilService;
use App\Domains\ClassCouncil\Application\Services\AutomaticDecisionService;
use App\Domains\ClassCouncil\Domain\Contracts\AcademicFrameworkContract;
use App\Domains\ClassCouncil\Domain\Contracts\AcademicPoliciesContract;
use App\Domains\ClassCouncil\Domain\Contracts\EnrollmentContract;
use App\Domains\ClassCouncil\Domain\Contracts\GradebookContract;
use App\Domains\ClassCouncil\Domain\Contracts\RecordsLifecycleContract;
use App\Domains\ClassCouncil\Domain\Events\CouncilDecisionRecorded;
use App\Domains\ClassCouncil\Domain\Events\CouncilSessionArchived;
use App\Domains\ClassCouncil\Domain\Events\CouncilSessionConvened;
use App\Domains\ClassCouncil\Domain\Events\CouncilSessionCreated;
use App\Domains\ClassCouncil\Domain\Events\CouncilSessionPublished;
use App\Domains\ClassCouncil\Infrastructure\Contracts\AcademicFrameworkAdapter;
use App\Domains\ClassCouncil\Infrastructure\Contracts\AcademicPoliciesAdapter;
use App\Domains\ClassCouncil\Infrastructure\Contracts\EnrollmentAdapter;
use App\Domains\ClassCouncil\Infrastructure\Contracts\GradebookAdapter;
use App\Domains\ClassCouncil\Infrastructure\Contracts\RecordsLifecycleAdapter;
use App\Domains\ClassCouncil\Infrastructure\Listeners\ClassCouncilAuditLogger;
use App\Domains\ClassCouncil\Infrastructure\Models\CouncilDecision;
use App\Domains\ClassCouncil\Infrastructure\Models\CouncilDeliberation;
use App\Domains\ClassCouncil\Infrastructure\Models\CouncilSession;
use App\Domains\ClassCouncil\Infrastructure\Observers\ClassCouncilEntityObserver;
use App\Domains\ClassCouncil\Infrastructure\Policies\CouncilSessionPolicy;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

/**
 * Owning service provider for the Class Council engine (SPR-013). Registers the
 * cross-domain contract bindings (adapters — the only place the Class Council
 * domain sees foreign models), the application services and actions, observers,
 * policies and event listeners. Follows the SPR-009/SPR-010/SPR-011 precedent.
 */
final class ClassCouncilServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../../config/nexus-class-council.php',
            'nexus-class-council',
        );

        $this->app->singleton(ClassCouncilService::class);
        $this->app->singleton(AutomaticDecisionService::class);

        $this->app->bind(CreateCouncilSessionAction::class);
        $this->app->bind(ConveneCouncilSessionAction::class);
        $this->app->bind(RecordCouncilDecisionAction::class);
        $this->app->bind(RecordCouncilDeliberationAction::class);
        $this->app->bind(PublishCouncilSessionAction::class);
        $this->app->bind(ArchiveCouncilSessionAction::class);

        $this->app->bind(AcademicFrameworkContract::class, AcademicFrameworkAdapter::class);
        $this->app->bind(AcademicPoliciesContract::class, AcademicPoliciesAdapter::class);
        $this->app->bind(EnrollmentContract::class, EnrollmentAdapter::class);
        $this->app->bind(GradebookContract::class, GradebookAdapter::class);
        $this->app->bind(RecordsLifecycleContract::class, RecordsLifecycleAdapter::class);
    }

    public function boot(): void
    {
        $this->registerObservers();
        $this->registerPolicies();
        $this->registerEvents();
    }

    private function registerObservers(): void
    {
        $observer = ClassCouncilEntityObserver::class;

        CouncilSession::observe($observer);
        CouncilDecision::observe($observer);
        CouncilDeliberation::observe($observer);
    }

    private function registerPolicies(): void
    {
        Gate::policy(CouncilSession::class, CouncilSessionPolicy::class);
    }

    private function registerEvents(): void
    {
        Event::listen(CouncilSessionCreated::class, [ClassCouncilAuditLogger::class, 'handleCreated']);
        Event::listen(CouncilSessionConvened::class, [ClassCouncilAuditLogger::class, 'handleConvened']);
        Event::listen(CouncilDecisionRecorded::class, [ClassCouncilAuditLogger::class, 'handleDecisionRecorded']);
        Event::listen(CouncilSessionPublished::class, [ClassCouncilAuditLogger::class, 'handlePublished']);
        Event::listen(CouncilSessionArchived::class, [ClassCouncilAuditLogger::class, 'handleArchived']);
    }
}
