<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Domain\Exceptions;

/**
 * Thrown when a score is recorded for an enrollment that is not active in the
 * assessment's class (SPR-009).
 */
final class StudentNotEnrolledException extends AssessmentException
{
    public static function forEnrollment(int|string $enrollmentId, int|string $classId): self
    {
        return new self("Enrollment [{$enrollmentId}] is not actively enrolled in class [{$classId}].");
    }
}
