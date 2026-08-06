<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Domain\Exceptions;

/**
 * Thrown when a mutating action is attempted on a Locked or Archived session.
 * Locked sessions are immutable by design (SPR-008).
 */
final class SessionLockedException extends AttendanceException
{
    public static function forSession(int|string $sessionId): self
    {
        return new self("Attendance session [{$sessionId}] is locked and immutable.");
    }
}
