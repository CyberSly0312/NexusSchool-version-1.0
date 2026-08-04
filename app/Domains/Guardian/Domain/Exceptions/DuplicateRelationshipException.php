<?php

declare(strict_types=1);

namespace App\Domains\Guardian\Domain\Exceptions;

use RuntimeException;

/**
 * Raised when an active relationship already exists between a guardian and a
 * student, or when a relationship-type code is unknown for the school.
 */
final class DuplicateRelationshipException extends RuntimeException
{
    public static function existing(int $studentId, int $guardianId): self
    {
        return new self(
            "A relationship already exists between guardian #{$guardianId} and student #{$studentId}.",
        );
    }

    public static function message(string $message): self
    {
        return new self($message);
    }
}
