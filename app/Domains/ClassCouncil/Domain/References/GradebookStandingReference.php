<?php

declare(strict_types=1);

namespace App\Domains\ClassCouncil\Domain\References;

/**
 * Student-level academic aggregates of one student over one period (SPR-013).
 * Supplied by the Gradebook engine through the contract — the Class Council
 * engine reads them verbatim and never recomputes a figure.
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
