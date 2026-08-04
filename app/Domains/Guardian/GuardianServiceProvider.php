<?php

declare(strict_types=1);

namespace App\Domains\Guardian;

use App\Domains\Guardian\Application\Services\GuardianNumberGenerator;
use App\Domains\Guardian\Application\Services\GuardianService;
use App\Domains\Guardian\Domain\Events\EmergencyContactAssigned;
use App\Domains\Guardian\Domain\Events\GuardianCreated;
use App\Domains\Guardian\Domain\Events\GuardianLinked;
use App\Domains\Guardian\Domain\Events\GuardianUnlinked;
use App\Domains\Guardian\Domain\Events\GuardianUpdated;
use App\Domains\Guardian\Domain\Events\PickupAuthorizationGranted;
use App\Domains\Guardian\Domain\Events\PrimaryGuardianAssigned;
use App\Domains\Guardian\Infrastructure\Listeners\GuardianAuditLogger;
use App\Domains\Guardian\Infrastructure\Models\Guardian;
use App\Domains\Guardian\Infrastructure\Models\RelationshipType;
use App\Domains\Guardian\Infrastructure\Models\StudentGuardianRelationship;
use App\Domains\Guardian\Infrastructure\Models\StudentGuardianRelationshipHistory;
use App\Domains\Guardian\Infrastructure\Observers\GuardianEntityObserver;
use App\Domains\Guardian\Infrastructure\Policies\GuardianPolicy;
use App\Domains\Guardian\Infrastructure\Policies\StudentGuardianRelationshipPolicy;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

/**
 * Owning service provider for the Guardian domain (SPR-005). Registers the
 * domain's bindings, observers, policies and event listeners; follows the
 * SPR-002 decision D4 of keeping the legacy EventServiceProvider untouched.
 */
final class GuardianServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(GuardianNumberGenerator::class);
        $this->app->singleton(GuardianService::class);
    }

    public function boot(): void
    {
        $this->registerObservers();
        $this->registerPolicies();
        $this->registerEvents();
    }

    private function registerObservers(): void
    {
        $observer = GuardianEntityObserver::class;

        Guardian::observe($observer);
        StudentGuardianRelationship::observe($observer);
        RelationshipType::observe($observer);
        StudentGuardianRelationshipHistory::observe($observer);
    }

    private function registerPolicies(): void
    {
        Gate::policy(Guardian::class, GuardianPolicy::class);
        Gate::policy(StudentGuardianRelationship::class, StudentGuardianRelationshipPolicy::class);
    }

    private function registerEvents(): void
    {
        Event::listen(GuardianCreated::class, [GuardianAuditLogger::class, 'handleGuardianCreated']);
        Event::listen(GuardianUpdated::class, [GuardianAuditLogger::class, 'handleGuardianUpdated']);
        Event::listen(GuardianLinked::class, [GuardianAuditLogger::class, 'handleGuardianLinked']);
        Event::listen(GuardianUnlinked::class, [GuardianAuditLogger::class, 'handleGuardianUnlinked']);
        Event::listen(PrimaryGuardianAssigned::class, [GuardianAuditLogger::class, 'handlePrimaryGuardianAssigned']);
        Event::listen(EmergencyContactAssigned::class, [GuardianAuditLogger::class, 'handleEmergencyContactAssigned']);
        Event::listen(PickupAuthorizationGranted::class, [GuardianAuditLogger::class, 'handlePickupAuthorizationGranted']);
    }
}
