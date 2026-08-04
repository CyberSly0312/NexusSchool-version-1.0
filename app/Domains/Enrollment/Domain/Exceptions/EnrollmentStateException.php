<?php

declare(strict_types=1);

namespace App\Domains\Enrollment\Domain\Exceptions;

use App\Domains\Enrollment\Domain\Enums\EnrollmentStatus;
use RuntimeException;

/**
 * Raised when a lifecycle transition is attempted from an incompatible status.
 * Guards the enrollment state machine defined in SPR-004.
 */
final class EnrollmentStateException extends RuntimeException
{
    public static function transition(EnrollmentStatus $from, string $target): self
    {
        return new self(
            "An enrollment cannot transition from {$from->value} to {$target}.",
        );
    }

    public static function immutable(int $enrollmentId): self
    {
        return new self("Enrollment #{$enrollmentId} is archived and cannot be modified.");
    }

    public static function message(string $message): self
    {
        return new self($message);
    }
}
