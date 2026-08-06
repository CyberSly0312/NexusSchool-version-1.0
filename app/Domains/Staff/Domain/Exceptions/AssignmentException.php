<?php

declare(strict_types=1);

namespace App\Domains\Staff\Domain\Exceptions;

use RuntimeException;

/**
 * Raised when an assignment violates the staff assignment invariants
 * (same-school validation, no overlapping assignment of the same kind).
 */
final class AssignmentException extends RuntimeException
{
    public static function departmentMismatch(int $departmentId): self
    {
        return new self("Department {$departmentId} does not belong to this school.");
    }

    public static function positionMismatch(int $positionId): self
    {
        return new self("Position {$positionId} does not belong to this school.");
    }

    public static function staffImmutable(int $staffId): self
    {
        return new self("Staff member {$staffId} cannot receive new assignments.");
    }

    public static function overlapping(string $type): self
    {
        return new self("Staff member already has a current {$type} assignment.");
    }

    public static function message(string $message): self
    {
        return new self($message);
    }
}
