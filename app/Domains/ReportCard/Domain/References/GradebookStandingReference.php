<?php

declare(strict_types=1);

namespace App\Domains\ReportCard\Domain\References;

/**
 * Student-level aggregates rendered on a bulletin (SPR-011).
 *
 * The GPA, CGPA, overall percentage and academic standing are computed by the
 * Gradebook engine — the Report Card engine only reads them through its
 * contract and never performs any calculation.
 */
final readonly class GradebookStandingReference
{
    public function __construct(
        public ?float $gpa = null,
        public ?float $cgpa = null,
        public ?float $overallPercentage = null,
        public ?string $academicStanding = null,
        public int $failedSubjects = 0,
    ) {
    }
}
