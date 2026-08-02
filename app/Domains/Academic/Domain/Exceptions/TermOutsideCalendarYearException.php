<?php

declare(strict_types=1);

namespace App\Domains\Academic\Domain\Exceptions;

use RuntimeException;

/**
 * Thrown when a term's dates fall outside the enclosing calendar year's range.
 */
final class TermOutsideCalendarYearException extends RuntimeException
{
    public static function create(string $termName, string $yearName): self
    {
        return new self(
            "Term [{$termName}] must be fully contained within calendar year [{$yearName}]."
        );
    }
}
