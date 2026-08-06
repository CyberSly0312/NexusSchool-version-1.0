<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication;

use App\Domains\AcademicCommunication\Application\AcademicCommunicationService;
use App\Domains\AcademicCommunication\Application\Actions\ApproveDocumentAction;
use App\Domains\AcademicCommunication\Application\Actions\ArchiveDocumentAction;
use App\Domains\AcademicCommunication\Application\Actions\GenerateChronicleAction;
use App\Domains\AcademicCommunication\Application\Actions\GenerateCouncilMinutesAction;
use App\Domains\AcademicCommunication\Application\Actions\GenerateDocumentAction;
use App\Domains\AcademicCommunication\Application\Actions\GenerateParentSummaryAction;
use App\Domains\AcademicCommunication\Application\Actions\GenerateReportCardAction;
use App\Domains\AcademicCommunication\Application\Actions\GenerateStudentSummaryAction;
use App\Domains\AcademicCommunication\Application\Actions\GenerateTeacherSummaryAction;
use App\Domains\AcademicCommunication\Application\Actions\PublishDocumentAction;
use App\Domains\AcademicCommunication\Application\Actions\RegenerateDocumentAction;
use App\Domains\AcademicCommunication\Application\Actions\ReviewDocumentAction;
use App\Domains\AcademicCommunication\Application\Services\DocumentContentComposer;
use App\Domains\AcademicCommunication\Application\Services\DocumentNumberGenerator;
use App\Domains\AcademicCommunication\Application\Services\PublicationDispatcher;
use App\Domains\AcademicCommunication\Application\Services\PublicationRecipientResolver;
use App\Domains\AcademicCommunication\Domain\Contracts\AcademicFrameworkContract;
use App\Domains\AcademicCommunication\Domain\Contracts\AcademicPoliciesContract;
use App\Domains\AcademicCommunication\Domain\Contracts\AssessmentContract;
use App\Domains\AcademicCommunication\Domain\Contracts\AttendanceContract;
use App\Domains\AcademicCommunication\Domain\Contracts\ClassCouncilContract;
use App\Domains\AcademicCommunication\Domain\Contracts\DocumentRendererContract;
use App\Domains\AcademicCommunication\Domain\Contracts\GradebookContract;
use App\Domains\AcademicCommunication\Domain\Contracts\GuardianContract;
use App\Domains\AcademicCommunication\Domain\Contracts\SchoolContract;
use App\Domains\AcademicCommunication\Domain\Contracts\StaffContract;
use App\Domains\AcademicCommunication\Domain\Contracts\StudentIdentityContract;
use App\Domains\AcademicCommunication\Domain\Events\DocumentApproved;
use App\Domains\AcademicCommunication\Domain\Events\DocumentArchived;
use App\Domains\AcademicCommunication\Domain\Events\DocumentGenerated;
use App\Domains\AcademicCommunication\Domain\Events\DocumentPublished;
use App\Domains\AcademicCommunication\Domain\Events\DocumentVersionCreated;
use App\Domains\AcademicCommunication\Domain\Events\PublicationCancelled;
use App\Domains\AcademicCommunication\Domain\Events\PublicationCompleted;
use App\Domains\AcademicCommunication\Infrastructure\Contracts\AcademicFrameworkAdapter;
use App\Domains\AcademicCommunication\Infrastructure\Contracts\AcademicPoliciesAdapter;
use App\Domains\AcademicCommunication\Infrastructure\Contracts\AssessmentAdapter;
use App\Domains\AcademicCommunication\Infrastructure\Contracts\AttendanceAdapter;
use App\Domains\AcademicCommunication\Infrastructure\Contracts\ClassCouncilAdapter;
use App\Domains\AcademicCommunication\Infrastructure\Contracts\GradebookAdapter;
use App\Domains\AcademicCommunication\Infrastructure\Contracts\GuardianAdapter;
use App\Domains\AcademicCommunication\Infrastructure\Contracts\SchoolAdapter;
use App\Domains\AcademicCommunication\Infrastructure\Contracts\StaffAdapter;
use App\Domains\AcademicCommunication\Infrastructure\Contracts\StudentIdentityAdapter;
use App\Domains\AcademicCommunication\Infrastructure\Listeners\AcademicCommunicationAuditLogger;
use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocument;
use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocumentTemplate;
use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocumentVersion;
use App\Domains\AcademicCommunication\Infrastructure\Models\DocumentApproval;
use App\Domains\AcademicCommunication\Infrastructure\Models\DocumentAudit;
use App\Domains\AcademicCommunication\Infrastructure\Models\DocumentRecipient;
use App\Domains\AcademicCommunication\Infrastructure\Models\DocumentSignature;
use App\Domains\AcademicCommunication\Infrastructure\Models\GeneratedFile;
use App\Domains\AcademicCommunication\Infrastructure\Models\PublicationChannel;
use App\Domains\AcademicCommunication\Infrastructure\Models\PublicationDeliveryAttempt;
use App\Domains\AcademicCommunication\Infrastructure\Models\PublicationPolicy;
use App\Domains\AcademicCommunication\Infrastructure\Models\PublicationRecord;
use App\Domains\AcademicCommunication\Infrastructure\Observers\AcademicCommunicationEntityObserver;
use App\Domains\AcademicCommunication\Infrastructure\Policies\AcademicDocumentPolicy;
use App\Domains\AcademicCommunication\Infrastructure\Policies\AcademicDocumentTemplatePolicy;
use App\Domains\AcademicCommunication\Infrastructure\Policies\PublicationPolicyPolicy;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

/**
 * Owning service provider for the Academic Communication Engine (SPR-012).
 *
 * Registers the engine's application services and actions, the cross-domain
 * contract bindings (adapters — the only place this engine sees foreign
 * models), the renderer, the channel drivers, and the observer / policies /
 * event listeners. Follows the SPR-009 Assessment provider precedent.
 */
final class AcademicCommunicationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../../config/nexus-academic-communication.php',
            'nexus-academic-communication',
        );

        $this->app->singleton(AcademicCommunicationService::class);
        $this->app->singleton(DocumentNumberGenerator::class);
        $this->app->singleton(DocumentContentComposer::class);
        $this->app->singleton(PublicationRecipientResolver::class);

        $this->app->singleton(PublicationDispatcher::class, function ($app) {
            return new PublicationDispatcher(
                (array) config('nexus-academic-communication.channels', []),
                $app,
            );
        });

        $this->app->singleton(DocumentRendererContract::class, function ($app) {
            $class = (string) config('nexus-academic-communication.renderer', \App\Domains\AcademicCommunication\Infrastructure\Renderers\HtmlDocumentRenderer::class);

            return $app->make($class);
        });

        $this->app->bind(GenerateDocumentAction::class);
        $this->app->bind(GenerateReportCardAction::class);
        $this->app->bind(GenerateCouncilMinutesAction::class);
        $this->app->bind(GenerateParentSummaryAction::class);
        $this->app->bind(GenerateTeacherSummaryAction::class);
        $this->app->bind(GenerateStudentSummaryAction::class);
        $this->app->bind(GenerateChronicleAction::class);
        $this->app->bind(ReviewDocumentAction::class);
        $this->app->bind(ApproveDocumentAction::class);
        $this->app->bind(PublishDocumentAction::class);
        $this->app->bind(ArchiveDocumentAction::class);
        $this->app->bind(RegenerateDocumentAction::class);

        $this->app->bind(AcademicFrameworkContract::class, AcademicFrameworkAdapter::class);
        $this->app->bind(SchoolContract::class, SchoolAdapter::class);
        $this->app->bind(StudentIdentityContract::class, StudentIdentityAdapter::class);
        $this->app->bind(GuardianContract::class, GuardianAdapter::class);
        $this->app->bind(StaffContract::class, StaffAdapter::class);
        $this->app->bind(AttendanceContract::class, AttendanceAdapter::class);
        $this->app->bind(AssessmentContract::class, AssessmentAdapter::class);
        $this->app->bind(GradebookContract::class, GradebookAdapter::class);
        $this->app->bind(ClassCouncilContract::class, ClassCouncilAdapter::class);
        $this->app->bind(AcademicPoliciesContract::class, AcademicPoliciesAdapter::class);
    }

    public function boot(): void
    {
        $this->registerObservers();
        $this->registerPolicies();
        $this->registerEvents();
    }

    private function registerObservers(): void
    {
        $observer = AcademicCommunicationEntityObserver::class;

        AcademicDocument::observe($observer);
        AcademicDocumentVersion::observe($observer);
        AcademicDocumentTemplate::observe($observer);
        PublicationPolicy::observe($observer);
        PublicationChannel::observe($observer);
        PublicationRecord::observe($observer);
        PublicationDeliveryAttempt::observe($observer);
        DocumentApproval::observe($observer);
        DocumentSignature::observe($observer);
        DocumentRecipient::observe($observer);
        DocumentAudit::observe($observer);
        GeneratedFile::observe($observer);
    }

    private function registerPolicies(): void
    {
        Gate::policy(AcademicDocument::class, AcademicDocumentPolicy::class);
        Gate::policy(AcademicDocumentTemplate::class, AcademicDocumentTemplatePolicy::class);
        Gate::policy(PublicationPolicy::class, PublicationPolicyPolicy::class);
    }

    private function registerEvents(): void
    {
        Event::listen(DocumentGenerated::class, [AcademicCommunicationAuditLogger::class, 'handleGenerated']);
        Event::listen(DocumentVersionCreated::class, [AcademicCommunicationAuditLogger::class, 'handleVersionCreated']);
        Event::listen(DocumentApproved::class, [AcademicCommunicationAuditLogger::class, 'handleApproved']);
        Event::listen(DocumentPublished::class, [AcademicCommunicationAuditLogger::class, 'handlePublished']);
        Event::listen(DocumentArchived::class, [AcademicCommunicationAuditLogger::class, 'handleArchived']);
        Event::listen(PublicationCompleted::class, [AcademicCommunicationAuditLogger::class, 'handlePublicationCompleted']);
        Event::listen(PublicationCancelled::class, [AcademicCommunicationAuditLogger::class, 'handlePublicationCancelled']);
    }
}
