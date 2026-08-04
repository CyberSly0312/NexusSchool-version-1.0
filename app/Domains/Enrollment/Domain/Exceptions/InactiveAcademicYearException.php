<?php

declare(strict_types=1);

namespace App\Domains\Enrollment\Domain\Exceptions;

use RuntimeException;

/**
 * Raised when an enrollment references an academic year that is not active.
 */
final class InactiveAcademicYearException extends RuntimeException
{
    public static function message(string $message): self
    {
        return new self($message);
    }
}
