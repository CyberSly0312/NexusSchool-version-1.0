<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Domain\Exceptions;

use RuntimeException;

/**
 * Base exception of the Attendance engine (SPR-008).
 */
class AttendanceException extends RuntimeException
{
}
