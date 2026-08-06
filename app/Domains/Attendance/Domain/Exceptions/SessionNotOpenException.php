<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Domain\Exceptions;

/**
 * Thrown when records are written to a session that is not open for recording.
 */
final class SessionNotOpenException extends AttendanceException
{
    public static function forSession(int|string $sessionId, string $status): self
    {
        return new self("Attendance session [{$sessionId}] is not open for recording (current state: {$status}).");
    }
}
