<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Domain\Exceptions;

use App\Domains\Attendance\Domain\Enums\AttendanceSessionStatus;

/**
 * Thrown when a session cannot move from its current lifecycle state to the
 * requested one (SPR-008).
 */
final class InvalidStatusTransitionException extends AttendanceException
{
    public static function from(AttendanceSessionStatus $from, AttendanceSessionStatus $to): self
    {
        return new self(
            "Cannot transition attendance session from [{$from->value}] to [{$to->value}].",
        );
    }
}
