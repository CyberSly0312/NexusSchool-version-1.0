<?php

declare(strict_types=1);

namespace App\Domains\Gradebook\Domain\References;

/**
 * One published grade line as an input of the student-level aggregations
 * (SPR-010). The grade point is the canonical value stored on the grade entry;
 * the coefficient is the one of the owning gradebook.
 */
final readonly class StandingContributionReference
{
    public function __construct(
        public float $percentage,
        public float $gradePoint,
        public float $coefficient = 1.0,
    ) {
    }
}
