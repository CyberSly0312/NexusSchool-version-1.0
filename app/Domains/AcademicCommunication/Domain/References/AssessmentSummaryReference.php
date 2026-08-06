<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Domain\References;

/**
 * Assessment aggregate consumed from the Assessment engine (SPR-012). The
 * Assessment engine supplies the academic content; this engine renders the
 * official document.
 */
final readonly class AssessmentSummaryReference
{
    public function __construct(
        public SubjectReference $subject,
        public int $assessmentCount,
        public float $bestScore,
        public float $worstScore,
        public float $average,
        public float $passRate,
    ) {
    }
}
