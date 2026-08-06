<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Infrastructure\Listeners;

use App\Domains\Assessment\Domain\Events\AssessmentArchived;
use App\Domains\Assessment\Domain\Events\AssessmentCompleted;
use App\Domains\Assessment\Domain\Events\AssessmentCreated;
use App\Domains\Assessment\Domain\Events\AssessmentPublished;
use App\Domains\Assessment\Domain\Events\AssessmentStatisticsGenerated;
use App\Domains\Assessment\Domain\Events\ScoreRecorded;
use App\Domains\Assessment\Domain\Events\ScoreUpdated;
use App\Domains\Assessment\Infrastructure\Models\Assessment;
use App\Domains\Assessment\Infrastructure\Models\AssessmentScore;
use App\Domains\Assessment\Infrastructure\Models\AssessmentStatistics;
use App\Models\AuditLog;
use App\Models\User;

/**
 * Writes the security audit trail (AuditLog) for every Assessment event
 * (SPR-009): creation, publishing, archiving, score recording/updating,
 * completion and statistics generation.
 */
final class AssessmentAuditLogger
{
    public function handleCreated(AssessmentCreated $event): void
    {
        $this->log(
            $event->assessment,
            'assessment_created',
            "Assessment [{$event->assessment->title}] created",
            $event->actor,
            $this->assessmentPayload($event->assessment),
        );
    }

    public function handlePublished(AssessmentPublished $event): void
    {
        $this->log(
            $event->assessment,
            'assessment_published',
            "Assessment [{$event->assessment->title}] published",
            $event->actor,
            $this->assessmentPayload($event->assessment),
        );
    }

    public function handleArchived(AssessmentArchived $event): void
    {
        $this->log(
            $event->assessment,
            'assessment_archived',
            "Assessment [{$event->assessment->title}] archived",
            $event->actor,
            $this->assessmentPayload($event->assessment),
        );
    }

    public function handleScoreRecorded(ScoreRecorded $event): void
    {
        $this->log(
            $event->score,
            'assessment_score_recorded',
            "Score [{$event->score->score}] recorded for enrollment #{$event->enrollmentId}",
            $event->actor,
            [
                'assessment_id' => $event->assessment->getKey(),
                'enrollment_id' => $event->enrollmentId,
                'score' => $event->score->score,
                'percentage' => $event->score->percentage,
            ],
        );
    }

    public function handleScoreUpdated(ScoreUpdated $event): void
    {
        $this->log(
            $event->score,
            'assessment_score_updated',
            "Score updated for enrollment #{$event->enrollmentId} [{$event->previousScore} → {$event->score->score}]",
            $event->actor,
            [
                'assessment_id' => $event->assessment->getKey(),
                'enrollment_id' => $event->enrollmentId,
                'previous_score' => $event->previousScore,
                'new_score' => $event->score->score,
            ],
        );
    }

    public function handleCompleted(AssessmentCompleted $event): void
    {
        $this->log(
            $event->assessment,
            'assessment_completed',
            "Assessment [{$event->assessment->title}] completed with {$event->scoresCount} scores",
            $event->actor,
            [
                'scores_count' => $event->scoresCount,
            ],
        );
    }

    public function handleStatisticsGenerated(AssessmentStatisticsGenerated $event): void
    {
        $this->log(
            $event->statistics,
            'assessment_statistics_generated',
            "Statistics generated for assessment [{$event->assessment->title}]",
            $event->actor,
            $this->statisticsPayload($event->statistics),
        );
    }

    /**
     * @param  array<string, mixed>|null  $payload
     */
    private function log(
        Assessment|AssessmentScore|AssessmentStatistics $auditable,
        string $action,
        string $description,
        ?User $actor = null,
        ?array $payload = null,
    ): void {
        AuditLog::create([
            'school_id' => $auditable->school_id,
            'auditable_type' => $auditable::class,
            'auditable_id' => $auditable->getKey(),
            'action' => $action,
            'event' => class_basename($auditable),
            'description' => $description,
            'user_id' => $actor?->getKey(),
            'user_type' => $actor !== null ? $actor::class : null,
            'new_values' => $payload,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function assessmentPayload(Assessment $assessment): array
    {
        return [
            'subject_id' => $assessment->academic_subject_id,
            'class_id' => $assessment->school_class_id,
            'term_id' => $assessment->academic_calendar_term_id,
            'title' => $assessment->title,
            'maximum_score' => $assessment->maximum_score,
            'passing_score' => $assessment->passing_score,
            'weight' => $assessment->weight,
            'status' => $assessment->status->value,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function statisticsPayload(AssessmentStatistics $statistics): array
    {
        return [
            'total_scores' => $statistics->total_scores,
            'average' => $statistics->average,
            'minimum' => $statistics->minimum,
            'maximum' => $statistics->maximum,
            'median' => $statistics->median,
            'pass_rate' => $statistics->pass_rate,
            'completion_rate' => $statistics->completion_rate,
        ];
    }
}
