<?php

declare(strict_types=1);

namespace App\Domains\Assessment;

use App\Domains\Assessment\Application\Actions\ArchiveAssessmentAction;
use App\Domains\Assessment\Application\Actions\BulkRecordScoresAction;
use App\Domains\Assessment\Application\Actions\CreateAssessmentAction;
use App\Domains\Assessment\Application\Actions\GenerateAssessmentStatisticsAction;
use App\Domains\Assessment\Application\Actions\PublishAssessmentAction;
use App\Domains\Assessment\Application\Actions\RecordScoreAction;
use App\Domains\Assessment\Application\Actions\UpdateAssessmentAction;
use App\Domains\Assessment\Application\Actions\ValidateScoresAction;
use App\Domains\Assessment\Application\AssessmentService;
use App\Domains\Assessment\Application\Services\AssessmentCompletionService;
use App\Domains\Assessment\Application\Services\AssessmentScoreQuery;
use App\Domains\Assessment\Application\Services\AssessmentStatisticsCalculator;
use App\Domains\Assessment\Application\Services\ScoreValidationService;
use App\Domains\Assessment\Domain\Contracts\AcademicFrameworkContract;
use App\Domains\Assessment\Domain\Contracts\AttendanceContract;
use App\Domains\Assessment\Domain\Contracts\EnrollmentContract;
use App\Domains\Assessment\Domain\Contracts\StaffContract;
use App\Domains\Assessment\Domain\Contracts\StudentIdentityContract;
use App\Domains\Assessment\Domain\Contracts\TimetableContract;
use App\Domains\Assessment\Domain\Events\AssessmentArchived;
use App\Domains\Assessment\Domain\Events\AssessmentCompleted;
use App\Domains\Assessment\Domain\Events\AssessmentCreated;
use App\Domains\Assessment\Domain\Events\AssessmentPublished;
use App\Domains\Assessment\Domain\Events\AssessmentStatisticsGenerated;
use App\Domains\Assessment\Domain\Events\ScoreRecorded;
use App\Domains\Assessment\Domain\Events\ScoreUpdated;
use App\Domains\Assessment\Infrastructure\Contracts\AcademicFrameworkAdapter;
use App\Domains\Assessment\Infrastructure\Contracts\AttendanceAdapter;
use App\Domains\Assessment\Infrastructure\Contracts\EnrollmentAdapter;
use App\Domains\Assessment\Infrastructure\Contracts\StaffAdapter;
use App\Domains\Assessment\Infrastructure\Contracts\StudentIdentityAdapter;
use App\Domains\Assessment\Infrastructure\Contracts\TimetableAdapter;
use App\Domains\Assessment\Infrastructure\Listeners\AssessmentAuditLogger;
use App\Domains\Assessment\Infrastructure\Models\Assessment;
use App\Domains\Assessment\Infrastructure\Models\AssessmentAttachment;
use App\Domains\Assessment\Infrastructure\Models\AssessmentComponent;
use App\Domains\Assessment\Infrastructure\Models\AssessmentRemark;
use App\Domains\Assessment\Infrastructure\Models\AssessmentScore;
use App\Domains\Assessment\Infrastructure\Models\AssessmentSession;
use App\Domains\Assessment\Infrastructure\Models\AssessmentStatistics;
use App\Domains\Assessment\Infrastructure\Models\AssessmentTemplate;
use App\Domains\Assessment\Infrastructure\Models\AssessmentType;
use App\Domains\Assessment\Infrastructure\Models\AssessmentWeight;
use App\Domains\Assessment\Infrastructure\Observers\AssessmentEntityObserver;
use App\Domains\Assessment\Infrastructure\Policies\AssessmentPolicy;
use App\Domains\Assessment\Infrastructure\Policies\AssessmentScorePolicy;
use App\Domains\Assessment\Infrastructure\Policies\AssessmentSessionPolicy;
use App\Domains\Assessment\Infrastructure\Policies\AssessmentTemplatePolicy;
use App\Domains\Assessment\Infrastructure\Policies\AssessmentTypePolicy;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

/**
 * Owning service provider for the Assessment engine (SPR-009). Registers the
 * cross-domain contract bindings (adapters — the only place the Assessment
 * domain sees foreign models), the application services and actions, observers,
 * policies and event listeners. Follows the SPR-008 Attendance precedent.
 */
final class AssessmentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../../config/nexus-assessment.php',
            'nexus-assessment',
        );

        $this->app->singleton(AssessmentService::class);
        $this->app->singleton(AssessmentStatisticsCalculator::class);
        $this->app->singleton(ScoreValidationService::class);
        $this->app->singleton(AssessmentScoreQuery::class);
        $this->app->singleton(AssessmentCompletionService::class);

        $this->app->bind(CreateAssessmentAction::class);
        $this->app->bind(UpdateAssessmentAction::class);
        $this->app->bind(PublishAssessmentAction::class);
        $this->app->bind(ArchiveAssessmentAction::class);
        $this->app->bind(RecordScoreAction::class);
        $this->app->bind(BulkRecordScoresAction::class);
        $this->app->bind(ValidateScoresAction::class);
        $this->app->bind(GenerateAssessmentStatisticsAction::class);

        $this->app->bind(AcademicFrameworkContract::class, AcademicFrameworkAdapter::class);
        $this->app->bind(StudentIdentityContract::class, StudentIdentityAdapter::class);
        $this->app->bind(EnrollmentContract::class, EnrollmentAdapter::class);
        $this->app->bind(StaffContract::class, StaffAdapter::class);
        $this->app->bind(TimetableContract::class, TimetableAdapter::class);
        $this->app->bind(AttendanceContract::class, AttendanceAdapter::class);
    }

    public function boot(): void
    {
        $this->registerObservers();
        $this->registerPolicies();
        $this->registerEvents();
    }

    private function registerObservers(): void
    {
        $observer = AssessmentEntityObserver::class;

        Assessment::observe($observer);
        AssessmentType::observe($observer);
        AssessmentTemplate::observe($observer);
        AssessmentSession::observe($observer);
        AssessmentComponent::observe($observer);
        AssessmentScore::observe($observer);
        AssessmentWeight::observe($observer);
        AssessmentRemark::observe($observer);
        AssessmentAttachment::observe($observer);
        AssessmentStatistics::observe($observer);
    }

    private function registerPolicies(): void
    {
        Gate::policy(Assessment::class, AssessmentPolicy::class);
        Gate::policy(AssessmentType::class, AssessmentTypePolicy::class);
        Gate::policy(AssessmentTemplate::class, AssessmentTemplatePolicy::class);
        Gate::policy(AssessmentSession::class, AssessmentSessionPolicy::class);
        Gate::policy(AssessmentScore::class, AssessmentScorePolicy::class);
    }

    private function registerEvents(): void
    {
        Event::listen(AssessmentCreated::class, [AssessmentAuditLogger::class, 'handleCreated']);
        Event::listen(AssessmentPublished::class, [AssessmentAuditLogger::class, 'handlePublished']);
        Event::listen(AssessmentArchived::class, [AssessmentAuditLogger::class, 'handleArchived']);
        Event::listen(ScoreRecorded::class, [AssessmentAuditLogger::class, 'handleScoreRecorded']);
        Event::listen(ScoreUpdated::class, [AssessmentAuditLogger::class, 'handleScoreUpdated']);
        Event::listen(AssessmentCompleted::class, [AssessmentAuditLogger::class, 'handleCompleted']);
        Event::listen(AssessmentStatisticsGenerated::class, [AssessmentAuditLogger::class, 'handleStatisticsGenerated']);
    }
}
