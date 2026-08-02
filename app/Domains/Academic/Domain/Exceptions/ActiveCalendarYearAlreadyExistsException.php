<?php

declare(strict_types=1);

namespace App\Domains\Academic\Domain\Exceptions;

use RuntimeException;

/**
 * Thrown when a school tries to activate more than one academic year.
 */
final class ActiveCalendarYearAlreadyExistsException extends RuntimeException
{
    public static function forSchool(string $schoolId, string $name): self
    {
        return new self(
            "A calendar year is already active for school [{$schoolId}]. ".
            "Deactivate [{$name}] or archive the current active year first."
        );
    }
}
