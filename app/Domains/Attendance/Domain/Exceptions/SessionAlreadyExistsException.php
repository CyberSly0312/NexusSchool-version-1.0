<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Domain\Exceptions;

/**
 * Thrown when session generation collides with an existing session for the
 * same class + time slot (one session per class + slot, SPR-008).
 */
final class SessionAlreadyExistsException extends AttendanceException
{
    public static function forSlot(int|string $classId, string $date, int $periodNumber): self
    {
        return new self(
            "An attendance session already exists for class [{$classId}] on [{$date}] period [{$periodNumber}].",
        );
    }
}
