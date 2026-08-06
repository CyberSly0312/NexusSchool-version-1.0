<?php

declare(strict_types=1);

namespace App\Domains\Staff\Domain\Exceptions;

use RuntimeException;

/**
 * Raised when an employment operation violates the employment invariants
 * (one active employment per staff, immutable history, valid period).
 */
final class EmploymentException extends RuntimeException
{
    public static function alreadyActive(int $staffId): self
    {
        return new self("Staff member {$staffId} already has an active employment contract.");
    }

    public static function notFound(int $staffId): self
    {
        return new self("Staff member {$staffId} has no employment contract.");
    }

    public static function notActive(int $employmentId): self
    {
        return new self("Employment contract {$employmentId} is not active.");
    }

    public static function terminationBeforeStart(int $employmentId): self
    {
        return new self("The termination date must follow the employment start date of contract {$employmentId}.");
    }

    public static function message(string $message): self
    {
        return new self($message);
    }
}
