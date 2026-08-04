<?php

declare(strict_types=1);

namespace App\Domains\Guardian\Domain\Exceptions;

use RuntimeException;

/**
 * Raised when a relationship would end up with more than one primary guardian
 * for the same student. The invariant is enforced by the application actions.
 */
final class MultiplePrimaryGuardianException extends RuntimeException
{
    public static function forStudent(int $studentId): self
    {
        return new self(
            "Student #{$studentId} can have only one primary guardian.",
        );
    }

    public static function message(string $message): self
    {
        return new self($message);
    }
}
