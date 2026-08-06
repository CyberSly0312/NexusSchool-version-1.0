<?php

declare(strict_types=1);

namespace App\Domains\Timetable\Domain\Exceptions;

/**
 * Raised when a cross-domain contract rejects a request: a referenced teacher,
 * subject, class or room does not exist, is inactive, or belongs to another school.
 */
class ContractViolationException extends TimetableException
{
}
