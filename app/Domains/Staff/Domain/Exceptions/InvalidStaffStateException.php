<?php

declare(strict_types=1);

namespace App\Domains\Staff\Domain\Exceptions;

use App\Domains\Staff\Domain\Enums\StaffStatus;
use RuntimeException;

/**
 * Raised when a staff state transition is invalid (e.g. editing a terminated
 * staff member).
 */
final class InvalidStaffStateException extends RuntimeException
{
    public static function transition(StaffStatus $from, string $target): self
    {
        return new self("A staff member cannot transition from {$from->value} to {$target}.");
    }

    public static function immutable(int $staffId): self
    {
        return new self("Staff member {$staffId} is in an immutable state and cannot be modified.");
    }

    public static function message(string $message): self
    {
        return new self($message);
    }
}
