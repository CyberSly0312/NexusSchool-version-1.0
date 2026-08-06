<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Application\Actions;

use App\Domains\Assessment\Application\DTOs\BulkScoreItem;
use App\Domains\Assessment\Application\DTOs\BulkScoresData;
use App\Domains\Assessment\Application\Services\AssessmentCompletionService;
use App\Domains\Assessment\Application\Services\ScoreValidationService;
use App\Domains\Assessment\Domain\Contracts\EnrollmentContract;
use App\Domains\Assessment\Domain\Enums\ScoreStatus;
use App\Domains\Assessment\Domain\Events\ScoreRecorded;
use App\Domains\Assessment\Domain\ValueObjects\ScorePercentage;
use App\Domains\Assessment\Infrastructure\Models\Assessment;
use App\Domains\Assessment\Infrastructure\Models\AssessmentScore;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Bulk score write for a whole class (SPR-009).
 *
 * Performance: one query validates the class roster, one query filters already
 * scored enrollments, and inserts are chunked with Eloquent's mass insert so a
 * 200-student class stays at a constant, small query count. CSV-ready: rows
 * mirror the score table columns.
 */
final class BulkRecordScoresAction
{
    public function __construct(
        private readonly EnrollmentContract $enrollment,
        private readonly ScoreValidationService $validation,
        private readonly AssessmentCompletionService $completion,
    ) {
    }

    /**
     * @return array{inserted: int, skipped: int, rejected: int}
     */
    public function execute(BulkScoresData $data, ?User $actor = null): array
    {
        if ($data->items === []) {
            return ['inserted' => 0, 'skipped' => 0, 'rejected' => 0];
        }

        return DB::transaction(function () use ($data, $actor): array {
            $assessment = Assessment::query()->findOrFail($data->assessmentId);

            $assessment->assertScoreable();

            $allowed = $this->allowedEnrollmentIds($assessment);
            $recorded = $this->recordedEnrollmentKeys($assessment);

            $chunkSize = (int) config('nexus-assessment.bulk.chunk_size', 200);
            $inserted = 0;
            $skipped = 0;
            $rejected = 0;

            $rows = [];

            foreach ($data->items as $item) {
                if (! isset($allowed[$item->enrollmentId])) {
                    $rejected++;

                    continue;
                }

                $componentKey = $item->componentId ?? 0;
                $scoreKey = "{$componentKey}|{$item->enrollmentId}";

                if (isset($recorded[$scoreKey])) {
                    $skipped++;

                    continue;
                }

                if (! $this->isRangeValid($assessment, $item)) {
                    $rejected++;

                    continue;
                }

                $rows[] = $this->toRow($assessment, $item, $actor);

                if (count($rows) >= $chunkSize) {
                    $inserted += $this->flush($assessment, $rows, $actor);
                    $rows = [];
                }
            }

            if ($rows !== []) {
                $inserted += $this->flush($assessment, $rows, $actor);
            }

            if ($inserted > 0) {
                $this->completion->completeIfExpected($assessment, $actor);
            }

            return ['inserted' => $inserted, 'skipped' => $skipped, 'rejected' => $rejected];
        });
    }

    /**
     * @return array<int|string, true>
     */
    private function allowedEnrollmentIds(Assessment $assessment): array
    {
        $allowed = [];

        foreach ($this->enrollment->enrollmentsOfClass(
            $assessment->school_id,
            $assessment->school_class_id,
            $assessment->academic_calendar_year_id,
        ) as $enrollment) {
            $allowed[$enrollment->id] = true;
        }

        return $allowed;
    }

    /**
     * @return array<string, true>
     */
    private function recordedEnrollmentKeys(Assessment $assessment): array
    {
        $ids = AssessmentScore::query()
            ->where('assessment_id', $assessment->getKey())
            ->get(['assessment_component_id', 'enrollment_id'])
            ->map(fn (AssessmentScore $score): string => ($score->assessment_component_id ?? 0).'|'.$score->enrollment_id)
            ->all();

        return array_fill_keys($ids, true);
    }

    private function isRangeValid(Assessment $assessment, BulkScoreItem $item): bool
    {
        $maximum = $this->resolvedMaximum($assessment, $item->componentId);

        return ! $this->validation->validate($item->score, $maximum)->isInvalid();
    }

    private function resolvedMaximum(Assessment $assessment, int|string|null $componentId): float
    {
        if ($componentId === null) {
            return (float) $assessment->maximum_score;
        }

        $component = $assessment->components()->find($componentId);

        return $component !== null ? (float) $component->maximum_score : (float) $assessment->maximum_score;
    }

    /**
     * @return array<string, mixed>
     */
    private function toRow(Assessment $assessment, BulkScoreItem $item, ?User $actor): array
    {
        $maximum = $this->resolvedMaximum($assessment, $item->componentId);

        return [
            'uuid' => (string) Str::ulid(),
            'school_id' => $assessment->school_id,
            'assessment_id' => $assessment->getKey(),
            'assessment_component_id' => $item->componentId,
            'enrollment_id' => $item->enrollmentId,
            'score' => $item->score,
            'percentage' => ScorePercentage::fromScore($item->score, $maximum)->value,
            'status' => ScoreStatus::Draft->value,
            'validation_status' => $this->validation->validate($item->score, $maximum)->value,
            'feedback' => $item->feedback,
            'graded_by' => $actor?->getKey(),
            'graded_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     */
    private function flush(Assessment $assessment, array $rows, ?User $actor): int
    {
        AssessmentScore::query()->insert($rows);

        $keys = array_map(
            static fn (array $row): string => ($row['assessment_component_id'] ?? 0).'|'.$row['enrollment_id'],
            $rows,
        );

        $scores = AssessmentScore::query()
            ->where('assessment_id', $assessment->getKey())
            ->get()
            ->keyBy(fn (AssessmentScore $score): string => ($score->assessment_component_id ?? 0).'|'.$score->enrollment_id);

        foreach ($rows as $row) {
            $score = $scores->get(
                ($row['assessment_component_id'] ?? 0).'|'.$row['enrollment_id'],
            );

            if ($score === null) {
                continue;
            }

            ScoreRecorded::dispatch($assessment, $score, $row['enrollment_id'], $actor);
        }

        return count($rows);
    }
}
