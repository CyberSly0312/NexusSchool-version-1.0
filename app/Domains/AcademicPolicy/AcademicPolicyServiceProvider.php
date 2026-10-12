<?php

declare(strict_types=1);

namespace App\Domains\AcademicPolicy;

use App\Domains\AcademicPolicy\Application\Actions\ArchiveAcademicPolicyAction;
use App\Domains\AcademicPolicy\Application\Actions\CreateAcademicPolicyAction;
use App\Domains\AcademicPolicy\Application\Actions\UpdateAcademicPolicyAction;
use App\Domains\AcademicPolicy\Application\Services\AcademicPolicyService;
use App\Domains\AcademicPolicy\Domain\Contracts\AcademicPolicyQueryContract;
use App\Domains\AcademicPolicy\Domain\Contracts\AcademicPolicyRepositoryInterface;
use App\Domains\AcademicPolicy\Domain\Events\AcademicPolicyArchived;
use App\Domains\AcademicPolicy\Domain\Events\AcademicPolicyCreated;
use App\Domains\AcademicPolicy\Domain\Events\AcademicPolicyUpdated;
use App\Domains\AcademicPolicy\Infrastructure\Contracts\AcademicPolicyQueryAdapter;
use App\Domains\AcademicPolicy\Infrastructure\Listeners\AcademicPolicyAuditLogger;
use App\Domains\AcademicPolicy\Infrastructure\Models\AcademicPolicy;
use App\Domains\AcademicPolicy\Infrastructure\Observers\AcademicPolicyEntityObserver;
use App\Domains\AcademicPolicy\Infrastructure\Policies\AcademicPolicyPolicy;
use App\Domains\AcademicPolicy\Infrastructure\Repositories\EloquentAcademicPolicyRepository;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

/**
 * Owning service provider for the Academic Policies Engine (SPR-009.5).
 *
 * Registers the engine's repository, the read-only query contract used by
 * downstream domains, the application service and actions, the observer, the
 * authorization policy and the audit listeners. Follows the SPR-002 provider
 * precedent.
 */
final class AcademicPolicyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../../config/nexus-academic-policy.php',
            'nexus-academic-policy',
        );

        $this->app->singleton(AcademicPolicyRepositoryInterface::class, EloquentAcademicPolicyRepository::class);
        $this->app->singleton(AcademicPolicyQueryContract::class, AcademicPolicyQueryAdapter::class);
        $this->app->singleton(AcademicPolicyService::class);

        $this->app->bind(CreateAcademicPolicyAction::class);
        $this->app->bind(UpdateAcademicPolicyAction::class);
        $this->app->bind(ArchiveAcademicPolicyAction::class);
    }

    public function boot(): void
    {
        $this->registerObservers();
        $this->registerPolicies();
        $this->registerEvents();
    }

    private function registerObservers(): void
    {
        AcademicPolicy::observe(AcademicPolicyEntityObserver::class);
    }

    private function registerPolicies(): void
    {
        Gate::policy(AcademicPolicy::class, AcademicPolicyPolicy::class);
    }

    private function registerEvents(): void
    {
        Event::listen(AcademicPolicyCreated::class, [AcademicPolicyAuditLogger::class, 'handleCreated']);
        Event::listen(AcademicPolicyUpdated::class, [AcademicPolicyAuditLogger::class, 'handleUpdated']);
        Event::listen(AcademicPolicyArchived::class, [AcademicPolicyAuditLogger::class, 'handleArchived']);
    }
}
