<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Application\Services;

use App\Domains\Assessment\Infrastructure\Models\Assessment;
use App\Domains\Assessment\Infrastructure\Models\AssessmentScore;

/**
 * Bounded query access to AssessmentScore rows (SPR-009). Keeps bulk flows to a
 * small, predictable number of queries and prevents N+1.
 */
class AssessmentScoreQuery
{
    /**
     * Existing scores of an assessment keyed by "componentId|enrollmentId".
     *
     * @return array<string, AssessmentScore>
     */
    public function indexedByComponentAndEnrollment(Assessment $assessment): array
    {
        $indexed = [];

        foreach ($this->forAssessment($assessment) as $score) {
            $componentKey = $score->assessment_component_id ?? 0;
            $indexed["{$componentKey}|{$score->enrollment_id}"] = $score;
        }

        return $indexed;
    }

    /**
     * All scores of an assessment, eager-loading remarks for display.
     *
     * @return \Illuminate\Support\Collection<int, AssessmentScore>
     */
    public function forAssessment(Assessment $assessment): \Illuminate\Support\Collection
    {
        return AssessmentScore::query()
            ->with('remarks')
            ->where('assessment_id', $assessment->getKey())
            ->orderBy('enrollment_id')
            ->get();
    }
}
