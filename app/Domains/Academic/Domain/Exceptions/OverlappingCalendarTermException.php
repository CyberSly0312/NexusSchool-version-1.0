<?php

declare(strict_types=1);

namespace App\Domains\Academic\Domain\Exceptions;

use RuntimeException;

/**
 * Thrown when a term's date range overlaps another term of the same calendar year.
 */
final class OverlappingCalendarTermException extends RuntimeException
{
    public static function create(string $termName): self
    {
        return new self("Term [{$termName}] overlaps another term of the same calendar year.");
    }
}
