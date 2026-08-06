<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Domain\Exceptions;

/**
 * Thrown when an attempt is made to edit the definition of a published,
 * completed or archived assessment (SPR-009).
 */
final class AssessmentDefinitionImmutableException extends AssessmentException
{
    public static function forAssessment(int|string $assessmentId, string $status): self
    {
        return new self("Assessment [{$assessmentId}] is [{$status}] and its definition is immutable.");
    }
}
