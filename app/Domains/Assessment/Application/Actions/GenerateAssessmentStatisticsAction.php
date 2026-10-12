<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Application\Actions;

use App\Domains\Assessment\Application\DTOs\AssessmentStatisticsResult;
use App\Domains\Assessment\Application\Services\AssessmentStatisticsCalculator;
use App\Domains\Assessment\Domain\Contracts\AcademicPoliciesContract;
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
 *
 * When the assessment defines an explicit passing score, that absolute
 * threshold is applied to every row; otherwise the school-scoped
 * `academic.assessment.default_passing_percentage` policy is applied to each
 * row's maximum, falling back to the configurable default passing percentage
 * when the school did not define a policy.
 */
final class GenerateAssessmentStatisticsAction
{
    public function __construct(
        private readonly AssessmentStatisticsCalculator $calculator,
        private readonly EnrollmentContract $enrollment,
        private readonly AcademicPoliciesContract $policies,
    ) {
    }

    public function execute(Assessment $assessment, ?User $actor = null): AssessmentStatisticsResult
    {
        $rows = [];

        $passing = $assessment->passing_score !== null ? (float) $assessment->passing_score : null;

        foreach ($this->collectScores($assessment) as $score) {
            $row = [
                'score' => (float) $score->score,
                'maximum' => $this->maximumFor($assessment, $score),
            ];

            if ($passing !== null) {
                $row['passing'] = $passing;
            }

            $rows[] = $row;
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
            passRate: $this->calculator->passRate($rows, $this->defaultPassingFraction($assessment)),
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

    /**
     * Resolve the effective default passing fraction of a school: the
     * school-scoped policy when defined, otherwise the engine configuration.
     */
    private function defaultPassingFraction(Assessment $assessment): float
    {
        $key = (string) config('nexus-assessment.statistics.passing_percentage_key');
        $policy = $this->policies->policy($assessment->school_id, $key);

        $percentage = $policy !== null && is_numeric($policy->value)
            ? (float) $policy->value
            : (float) config('nexus-assessment.statistics.default_passing_percentage', 50.0);

        return $percentage / 100.0;
    }
}
