<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Application;

use App\Domains\Assessment\Application\Actions\ArchiveAssessmentAction;
use App\Domains\Assessment\Application\Actions\BulkRecordScoresAction;
use App\Domains\Assessment\Application\Actions\CreateAssessmentAction;
use App\Domains\Assessment\Application\Actions\GenerateAssessmentStatisticsAction;
use App\Domains\Assessment\Application\Actions\PublishAssessmentAction;
use App\Domains\Assessment\Application\Actions\RecordScoreAction;
use App\Domains\Assessment\Application\Actions\UpdateAssessmentAction;
use App\Domains\Assessment\Application\Actions\ValidateScoresAction;
use App\Domains\Assessment\Application\DTOs\BulkScoresData;
use App\Domains\Assessment\Application\DTOs\CreateAssessmentData;
use App\Domains\Assessment\Application\DTOs\RecordScoreData;
use App\Domains\Assessment\Application\DTOs\UpdateAssessmentData;
use App\Domains\Assessment\Infrastructure\Models\Assessment;
use App\Models\User;

/**
 * Application facade for the Assessment engine (SPR-009). Delegates to the
 * action objects so feature calls stay single-line and actions stay testable.
 */
final class AssessmentService
{
    public function __construct(
        private readonly CreateAssessmentAction $createAssessment,
        private readonly UpdateAssessmentAction $updateAssessment,
        private readonly PublishAssessmentAction $publishAssessment,
        private readonly ArchiveAssessmentAction $archiveAssessment,
        private readonly RecordScoreAction $recordScore,
        private readonly BulkRecordScoresAction $bulkRecordScores,
        private readonly ValidateScoresAction $validateScores,
        private readonly GenerateAssessmentStatisticsAction $generateStatistics,
    ) {
    }

    public function createAssessment(CreateAssessmentData $data, ?User $actor = null): Assessment
    {
        return $this->createAssessment->execute($data, $actor);
    }

    public function updateAssessment(UpdateAssessmentData $data, ?User $actor = null): Assessment
    {
        return $this->updateAssessment->execute($data, $actor);
    }

    public function publishAssessment(Assessment $assessment, ?User $actor = null): Assessment
    {
        return $this->publishAssessment->execute($assessment, $actor);
    }

    public function archiveAssessment(Assessment $assessment, ?User $actor = null): Assessment
    {
        return $this->archiveAssessment->execute($assessment, $actor);
    }

    public function recordScore(RecordScoreData $data, ?User $actor = null): void
    {
        $this->recordScore->execute($data, $actor);
    }

    /**
     * @return array{inserted: int, skipped: int, rejected: int}
     */
    public function bulkRecordScores(BulkScoresData $data, ?User $actor = null): array
    {
        return $this->bulkRecordScores->execute($data, $actor);
    }

    /**
     * @param  array{assessment_component_id?: int|string|null, from_score?: int, limit?: int}  $options
     *
     * @return array{validated: int, invalid: int}
     */
    public function validateScores(Assessment $assessment, array $options = []): array
    {
        return $this->validateScores->execute($assessment, $options);
    }

    public function generateStatistics(Assessment $assessment, ?User $actor = null)
    {
        return $this->generateStatistics->execute($assessment, $actor);
    }
}
