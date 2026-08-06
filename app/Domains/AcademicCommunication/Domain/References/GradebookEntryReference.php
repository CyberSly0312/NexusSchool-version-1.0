<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Domain\References;

/**
 * One gradebook line consumed from the Gradebook engine for a report card or
 * transcript (SPR-012). Carries no model, only validated academic values.
 */
final readonly class GradebookEntryReference
{
    /**
     * @param  array<string, mixed>  $assessmentBreakdown
     */
    public function __construct(
        public SubjectReference $subject,
        public float $average,
        public ?float $coefficient = null,
        public ?float $weightedAverage = null,
        public ?string $letterGrade = null,
        public ?string $appreciation = null,
        public ?int $rank = null,
        public array $assessmentBreakdown = [],
    ) {
    }
}
