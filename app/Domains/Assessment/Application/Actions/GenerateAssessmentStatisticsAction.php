<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Application\Actions;

use App\Domains\Assessment\Application\DTOs\AssessmentStatisticsResult;
use App\Domains\Assessment\Application\Services\AssessmentStatisticsCalculator;
use App\Domains\Assessment\Domain\Contracts\EnrollmentContract;
use App\Domains\Assessment\Domain\Enums\ScoreValidationStatus;
use App\Domains\Assessment\Domain\Events\AssessmentStatisticsGenerated;
use App\Domains\Assessment\Infrastructure\Models\Assessment;
use App\Domains\Assessment\Infrastructure\Models\AssessmentScore;
use App\Domains\Assessment\Infrastructure\Models\AssessmentStatistics;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Computes and persists official aggregate statistics for an assessment
 * (SPR-009). Only validated or drafted scores are included so rejected rows
 * cannot skew official figures.
 */
final class GenerateAssessmentStatisticsAction
{
    public function __construct(
        private readonly AssessmentStatisticsCalculator $calculator,
        private readonly EnrollmentContract $enrollment,
    ) {
    }

    public function execute(Assessment $assessment, ?User $actor = null): AssessmentStatisticsResult
    {
        $rows = [];

        foreach ($this->collectScores($assessment) as $score) {
            $rows[] = [
                'score' => (float) $score->score,
                'maximum' => $this->maximumFor($assessment, $score),
            ];
        }

        $expected = count($this->enrollment->enrollmentsOfClass(
            $assessment->school_id,
            $assessment->school_class_id,
            $assessment->academic_calendar_year_id,
        ));
        $completed = count($rows);

        $result = new AssessmentStatisticsResult(
            totalScores: $completed,
            average: $this->calculator->average($rows),
            minimum: $this->calculator->minimum($rows),
            maximum: $this->calculator->maximum($rows),
            median: $this->calculator->median($rows),
            passRate: $this->calculator->passRate($rows),
            completionRate: $expected > 0 ? (float) round(($completed / $expected) * 100.0, 2) : 0.0,
        );

        DB::transaction(function () use ($assessment, $result, $actor): AssessmentStatistics {
            $statistics = $assessment->statistics()->create([
                'uuid' => (string) Str::ulid(),
                'school_id' => $assessment->school_id,
                ...$result->toArray(),
                'generated_at' => now(),
                'generated_by' => $actor?->getKey(),
            ]);

            AssessmentStatisticsGenerated::dispatch($assessment, $statistics, $actor);

            return $statistics;
        });

        return $result;
    }

    /**
     * @return \Traversable<AssessmentScore>
     */
    private function collectScores(Assessment $assessment): \Traversable
    {
        return AssessmentScore::query()
            ->where('assessment_id', $assessment->getKey())
            ->whereIn('validation_status', [
                ScoreValidationStatus::Valid->value,
                ScoreValidationStatus::BelowPassing->value,
            ])
            ->get();
    }

    private function maximumFor(Assessment $assessment, AssessmentScore $score): float
    {
        if ($score->assessment_component_id === null) {
            return (float) $assessment->maximum_score;
        }

        $component = $assessment->components()->find($score->assessment_component_id);

        return $component !== null ? (float) $component->maximum_score : (float) $assessment->maximum_score;
    }
}
