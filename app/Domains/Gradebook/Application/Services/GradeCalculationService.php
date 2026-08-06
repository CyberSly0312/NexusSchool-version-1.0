<?php

declare(strict_types=1);

namespace App\Domains\Gradebook\Application\Services;

use App\Domains\Gradebook\Domain\References\AssessmentReference;

/**
 * Computes term averages from published assessment contributions (SPR-010).
 *
 * The term average is the weighted mean of the assessment percentages, the
 * weights being the assessment weights (percentages of the term grade). When
 * the sum of the weights is zero the plain mean is used as a fallback so a
 * gradebook with only unweighted assessments still resolves.
 */
final class GradeCalculationService
{
    /**
     * @param  list<AssessmentReference>  $contributions
     */
    public function calculateTermPercentage(array $contributions): ?float
    {
        if ($contributions === []) {
            return null;
        }

        $weighted = 0.0;
        $totalWeight = 0.0;

        foreach ($contributions as $contribution) {
            $weight = max(0.0, $contribution->weight);
            $weighted += $contribution->percentage * $weight;
            $totalWeight += $weight;
        }

        $percentage = $totalWeight > 0.0
            ? $weighted / $totalWeight
            : array_sum(array_map(static fn (AssessmentReference $c): float => $c->percentage, $contributions)) / count($contributions);

        return (float) round(max(0.0, min(100.0, $percentage)), (int) config('nexus-gradebook.grade_system.decimal_places', 2));
    }

    /**
     * Weighted mean of several term averages (used to build general averages).
     *
     * @param  array<float>  $percentages
     * @param  array<float>  $coefficients
     */
    public function calculateWeightedMean(array $percentages, array $coefficients): ?float
    {
        if ($percentages === []) {
            return null;
        }

        $weighted = 0.0;
        $totalWeight = 0.0;

        foreach ($percentages as $index => $percentage) {
            $coefficient = max(0.0, (float) ($coefficients[$index] ?? 1.0));
            $weighted += (float) $percentage * $coefficient;
            $totalWeight += $coefficient;
        }

        if ($totalWeight <= 0.0) {
            return null;
        }

        return (float) round(max(0.0, min(100.0, $weighted / $totalWeight)), (int) config('nexus-gradebook.grade_system.decimal_places', 2));
    }
}
