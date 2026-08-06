<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Domain\Exceptions;

/**
 * Thrown when a student is marked twice in the same session. A student may only
 * ever appear once per session (SPR-008).
 */
final class DuplicateRecordException extends AttendanceException
{
    public static function forEnrollment(int|string $sessionId, int|string $enrollmentId): self
    {
        return new self(
            "Enrollment [{$enrollmentId}] already has a record in attendance session [{$sessionId}].",
        );
    }
}
