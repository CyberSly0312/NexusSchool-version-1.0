<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Domain\Exceptions;

/**
 * Thrown when a correction is attempted without the required authorization
 * (SPR-008). Corrections are authorized mutations that never overwrite history.
 */
final class UnauthorizedCorrectionException extends AttendanceException
{
    public static function forRecord(int|string $recordId): self
    {
        return new self("Correction of attendance record [{$recordId}] requires authorization.");
    }
}
