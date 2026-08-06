<?php

declare(strict_types=1);

namespace App\Domains\Gradebook\Domain\References;

/**
 * Read-only projection of one assessment contribution to a term average
 * (SPR-010). The grade engine reads only published assessments; each one
 * carries the student's percentage and the weight of the assessment inside
 * the term grade.
 */
final readonly class AssessmentReference
{
    public function __construct(
        public int|string $id,
        public string $title,
        public float $percentage,
        public float $weight = 1.0,
        public float $maximumScore = 0.0,
        public int|string|null $assessmentTypeId = null,
    ) {
    }
}
