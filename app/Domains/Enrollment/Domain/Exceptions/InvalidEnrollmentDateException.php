<?php

declare(strict_types=1);

namespace App\Domains\Enrollment\Domain\Exceptions;

use RuntimeException;

/**
 * Raised when a lifecycle date falls outside the bounds of the enrollment's
 * academic year.
 */
final class InvalidEnrollmentDateException extends RuntimeException
{
    public static function message(string $message): self
    {
        return new self($message);
    }
}
