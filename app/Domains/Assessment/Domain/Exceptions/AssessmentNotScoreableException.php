<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Domain\Exceptions;

/**
 * Thrown when a score is recorded against an assessment that is completed or
 * archived (SPR-009). Only draft, scheduled and published assessments accept
 * scores.
 */
final class AssessmentNotScoreableException extends AssessmentException
{
    public static function forAssessment(int|string $assessmentId, string $status): self
    {
        return new self("Assessment [{$assessmentId}] is [{$status}] and no longer accepts scores.");
    }
}
