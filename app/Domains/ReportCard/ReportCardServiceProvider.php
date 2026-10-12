<?php

declare(strict_types=1);

namespace App\Domains\ReportCard;

use App\Domains\ReportCard\Application\Actions\ArchiveReportCardAction;
use App\Domains\ReportCard\Application\Actions\CreateReportCardAction;
use App\Domains\ReportCard\Application\Actions\GenerateReportCardAction;
use App\Domains\ReportCard\Application\Actions\PublishReportCardAction;
use App\Domains\ReportCard\Application\Actions\RegenerateReportCardAction;
use App\Domains\ReportCard\Application\ReportCardService;
use App\Domains\ReportCard\Application\Services\ReportCardContentComposer;
use App\Domains\ReportCard\Domain\Contracts\AcademicCommunicationContract;
use App\Domains\ReportCard\Domain\Contracts\AcademicFrameworkContract;
use App\Domains\ReportCard\Domain\Contracts\AcademicPoliciesContract;
use App\Domains\ReportCard\Domain\Contracts\AttendanceContract;
use App\Domains\ReportCard\Domain\Contracts\ClassCouncilContract;
use App\Domains\ReportCard\Domain\Contracts\EnrollmentContract;
use App\Domains\ReportCard\Domain\Contracts\GradebookContract;
use App\Domains\ReportCard\Domain\Contracts\RecordsLifecycleContract;
use App\Domains\ReportCard\Domain\Events\ReportCardArchived;
use App\Domains\ReportCard\Domain\Events\ReportCardCreated;
use App\Domains\ReportCard\Domain\Events\ReportCardGenerated;
use App\Domains\ReportCard\Domain\Events\ReportCardPublished;
use App\Domains\ReportCard\Domain\Events\ReportCardRegenerated;
use App\Domains\ReportCard\Infrastructure\Contracts\AcademicCommunicationAdapter;
use App\Domains\ReportCard\Infrastructure\Contracts\AcademicFrameworkAdapter;
use App\Domains\ReportCard\Infrastructure\Contracts\AcademicPoliciesAdapter;
use App\Domains\ReportCard\Infrastructure\Contracts\AttendanceAdapter;
use App\Domains\ReportCard\Infrastructure\Contracts\ClassCouncilAdapter;
use App\Domains\ReportCard\Infrastructure\Contracts\EnrollmentAdapter;
use App\Domains\ReportCard\Infrastructure\Contracts\GradebookAdapter;
use App\Domains\ReportCard\Infrastructure\Contracts\RecordsLifecycleAdapter;
use App\Domains\ReportCard\Infrastructure\Listeners\ReportCardAuditLogger;
use App\Domains\ReportCard\Infrastructure\Models\ReportCard;
use App\Domains\ReportCard\Infrastructure\Models\ReportCardEntry;
use App\Domains\ReportCard\Infrastructure\Observers\ReportCardEntityObserver;
use App\Domains\ReportCard\Infrastructure\Policies\ReportCardPolicy;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

/**
 * Owning service provider for the Report Card engine (SPR-011). Registers the
 * cross-domain contract bindings (adapters — the only place the Report Card
 * domain sees foreign models), the application services and actions, observers,
 * policies and event listeners. Follows the SPR-009/SPR-010 precedent.
 */
final class ReportCardServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../../config/nexus-report-card.php',
            'nexus-report-card',
        );

        $this->app->singleton(ReportCardService::class);
        $this->app->singleton(ReportCardContentComposer::class);

        $this->app->bind(CreateReportCardAction::class);
        $this->app->bind(GenerateReportCardAction::class);
        $this->app->bind(RegenerateReportCardAction::class);
        $this->app->bind(PublishReportCardAction::class);
        $this->app->bind(ArchiveReportCardAction::class);

        $this->app->bind(AcademicFrameworkContract::class, AcademicFrameworkAdapter::class);
        $this->app->bind(EnrollmentContract::class, EnrollmentAdapter::class);
        $this->app->bind(GradebookContract::class, GradebookAdapter::class);
        $this->app->bind(AttendanceContract::class, AttendanceAdapter::class);
        $this->app->bind(AcademicPoliciesContract::class, AcademicPoliciesAdapter::class);
        $this->app->bind(ClassCouncilContract::class, ClassCouncilAdapter::class);
        $this->app->bind(AcademicCommunicationContract::class, AcademicCommunicationAdapter::class);
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
        $observer = ReportCardEntityObserver::class;

        ReportCard::observe($observer);
        ReportCardEntry::observe($observer);
    }

    private function registerPolicies(): void
    {
        Gate::policy(ReportCard::class, ReportCardPolicy::class);
    }

    private function registerEvents(): void
    {
        Event::listen(ReportCardCreated::class, [ReportCardAuditLogger::class, 'handleCreated']);
        Event::listen(ReportCardGenerated::class, [ReportCardAuditLogger::class, 'handleGenerated']);
        Event::listen(ReportCardRegenerated::class, [ReportCardAuditLogger::class, 'handleRegenerated']);
        Event::listen(ReportCardPublished::class, [ReportCardAuditLogger::class, 'handlePublished']);
        Event::listen(ReportCardArchived::class, [ReportCardAuditLogger::class, 'handleArchived']);
    }
}
