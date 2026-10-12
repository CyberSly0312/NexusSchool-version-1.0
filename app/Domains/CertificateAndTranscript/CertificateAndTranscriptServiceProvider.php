<?php

declare(strict_types=1);

namespace App\Domains\CertificateAndTranscript;

use App\Domains\CertificateAndTranscript\Application\Actions\ArchiveCompletionRequestAction;
use App\Domains\CertificateAndTranscript\Application\Actions\CreateCompletionRequestAction;
use App\Domains\CertificateAndTranscript\Application\Actions\GenerateCertificateAction;
use App\Domains\CertificateAndTranscript\Application\Actions\GenerateTranscriptAction;
use App\Domains\CertificateAndTranscript\Application\Actions\PublishCompletionRequestAction;
use App\Domains\CertificateAndTranscript\Application\CompletionRequestService;
use App\Domains\CertificateAndTranscript\Application\Services\CertificateContentComposer;
use App\Domains\CertificateAndTranscript\Application\Services\CertificateEligibilityService;
use App\Domains\CertificateAndTranscript\Application\Services\TranscriptContentComposer;
use App\Domains\CertificateAndTranscript\Domain\Contracts\AcademicCommunicationContract;
use App\Domains\CertificateAndTranscript\Domain\Contracts\AcademicPoliciesContract;
use App\Domains\CertificateAndTranscript\Domain\Contracts\ClassCouncilContract;
use App\Domains\CertificateAndTranscript\Domain\Contracts\EnrollmentContract;
use App\Domains\CertificateAndTranscript\Domain\Contracts\ReportCardContract;
use App\Domains\CertificateAndTranscript\Domain\Contracts\StudentIdentityContract;
use App\Domains\CertificateAndTranscript\Domain\Events\CertificateEligibilityDetermined;
use App\Domains\CertificateAndTranscript\Domain\Events\CertificateGenerated;
use App\Domains\CertificateAndTranscript\Domain\Events\CompletionRequestArchived;
use App\Domains\CertificateAndTranscript\Domain\Events\CompletionRequestCreated;
use App\Domains\CertificateAndTranscript\Domain\Events\CompletionRequestPublished;
use App\Domains\CertificateAndTranscript\Domain\Events\TranscriptGenerated;
use App\Domains\CertificateAndTranscript\Infrastructure\Contracts\AcademicCommunicationAdapter;
use App\Domains\CertificateAndTranscript\Infrastructure\Contracts\AcademicPoliciesAdapter;
use App\Domains\CertificateAndTranscript\Infrastructure\Contracts\ClassCouncilAdapter;
use App\Domains\CertificateAndTranscript\Infrastructure\Contracts\EnrollmentAdapter;
use App\Domains\CertificateAndTranscript\Infrastructure\Contracts\ReportCardAdapter;
use App\Domains\CertificateAndTranscript\Infrastructure\Contracts\StudentIdentityAdapter;
use App\Domains\CertificateAndTranscript\Infrastructure\Listeners\CompletionRequestAuditLogger;
use App\Domains\CertificateAndTranscript\Infrastructure\Models\CompletionRequest;
use App\Domains\CertificateAndTranscript\Infrastructure\Models\CompletionRequestEntry;
use App\Domains\CertificateAndTranscript\Infrastructure\Observers\CompletionRequestEntityObserver;
use App\Domains\CertificateAndTranscript\Infrastructure\Policies\CompletionRequestPolicy;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

/**
 * Owning service provider for the Certificate & Transcript engine (SPR-015).
 * Registers the cross-domain contract bindings (adapters — the only place the
 * domain sees foreign models), the application services and actions, observers,
 * policies and event listeners. Mirrors the SPR-011 Report Card provider.
 */
final class CertificateAndTranscriptServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../../config/nexus-certificate-and-transcript.php',
            'nexus-certificate-and-transcript',
        );

        $this->app->singleton(CompletionRequestService::class);
        $this->app->singleton(TranscriptContentComposer::class);
        $this->app->singleton(CertificateContentComposer::class);
        $this->app->singleton(CertificateEligibilityService::class);

        $this->app->bind(CreateCompletionRequestAction::class);
        $this->app->bind(GenerateTranscriptAction::class);
        $this->app->bind(GenerateCertificateAction::class);
        $this->app->bind(PublishCompletionRequestAction::class);
        $this->app->bind(ArchiveCompletionRequestAction::class);

        $this->app->bind(ReportCardContract::class, ReportCardAdapter::class);
        $this->app->bind(ClassCouncilContract::class, ClassCouncilAdapter::class);
        $this->app->bind(AcademicPoliciesContract::class, AcademicPoliciesAdapter::class);
        $this->app->bind(EnrollmentContract::class, EnrollmentAdapter::class);
        $this->app->bind(StudentIdentityContract::class, StudentIdentityAdapter::class);
        $this->app->bind(AcademicCommunicationContract::class, AcademicCommunicationAdapter::class);
    }

    public function boot(): void
    {
        $this->registerObservers();
        $this->registerPolicies();
        $this->registerEvents();
    }

    private function registerObservers(): void
    {
        $observer = CompletionRequestEntityObserver::class;

        CompletionRequest::observe($observer);
        CompletionRequestEntry::observe($observer);
    }

    private function registerPolicies(): void
    {
        Gate::policy(CompletionRequest::class, CompletionRequestPolicy::class);
    }

    private function registerEvents(): void
    {
        Event::listen(CompletionRequestCreated::class, [CompletionRequestAuditLogger::class, 'handleCreated']);
        Event::listen(CertificateEligibilityDetermined::class, [CompletionRequestAuditLogger::class, 'handleEligibilityDetermined']);
        Event::listen(TranscriptGenerated::class, [CompletionRequestAuditLogger::class, 'handleTranscriptGenerated']);
        Event::listen(CertificateGenerated::class, [CompletionRequestAuditLogger::class, 'handleCertificateGenerated']);
        Event::listen(CompletionRequestPublished::class, [CompletionRequestAuditLogger::class, 'handlePublished']);
        Event::listen(CompletionRequestArchived::class, [CompletionRequestAuditLogger::class, 'handleArchived']);
    }
}
