<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Domain\Exceptions;

/**
 * Thrown when a cross-domain reference supplied through a contract is invalid
 * or does not belong to the school (SPR-008).
 */
final class ContractViolationException extends AttendanceException
{
}
