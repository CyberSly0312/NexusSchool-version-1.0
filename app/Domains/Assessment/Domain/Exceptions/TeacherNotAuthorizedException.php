<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Domain\Exceptions;

/**
 * Thrown when a teacher that has no teacher profile (or no assignment to the
 * assessment's subject) attempts to record or validate scores (SPR-009).
 */
final class TeacherNotAuthorizedException extends AssessmentException
{
    public static function forSubject(int|string $subjectId): self
    {
        return new self("The actor is not authorized to grade subject [{$subjectId}].");
    }

    public static function notATeacher(): self
    {
        return new self('The actor is not a teacher.');
    }
}
