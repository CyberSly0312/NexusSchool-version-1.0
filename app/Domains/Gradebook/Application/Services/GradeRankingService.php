<?php

declare(strict_types=1);

namespace App\Domains\Gradebook\Application\Services;

use App\Domains\Gradebook\Domain\References\RankingReference;

/**
 * Computes standard competition ranks ("1224") inside a gradebook (SPR-010).
 * Students with equal percentages share a rank and the following rank skips.
 */
final class GradeRankingService
{
    /**
     * Ranks entries by percentage, descending.
     *
     * @param  array<string, float>  $percentages  keyed by student id
     *
     * @return array<string, RankingReference>  keyed by student id
     */
    public function rank(array $percentages): array
    {
        if ($percentages === []) {
            return [];
        }

        $denominator = count($percentages);

        arsort($percentages, SORT_NUMERIC);

        $ranks = [];
        $current = null;
        $position = 0;

        foreach ($percentages as $studentId => $percentage) {
            $position++;
            if ($current === null || $percentage !== $current) {
                $current = $percentage;
                $rank = $position;
            }

            $ranks[(string) $studentId] = new RankingReference(
                studentId: $studentId,
                percentage: (float) $percentage,
                rank: $rank,
                denominator: $denominator,
            );
        }

        return $ranks;
    }
}
