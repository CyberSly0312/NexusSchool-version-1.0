<?php

declare(strict_types=1);

namespace App\Domains\ReportCard\Domain\References;

/**
 * One gradebook line rendered on a bulletin (SPR-011). The engine never
 * computes these figures: they are the validated term averages supplied by the
 * Gradebook engine.
 */
final readonly class GradebookEntryReference
{
    public function __construct(
        public int|string $subjectId,
        public string $subjectName,
        public string|null $subjectCode = null,
        public float $average = 0.0,
        public float $percentage = 0.0,
        public float $coefficient = 1.0,
        public float $gradePoint = 0.0,
        public ?string $letterGrade = null,
        public ?string $appreciation = null,
        public ?int $rank = null,
        public ?int $rankDenominator = null,
    ) {
    }
}
