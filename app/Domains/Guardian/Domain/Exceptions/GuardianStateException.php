<?php

declare(strict_types=1);

namespace App\Domains\Guardian\Domain\Exceptions;

use App\Domains\Guardian\Domain\Enums\GuardianStatus;
use RuntimeException;

/**
 * Raised when a lifecycle operation is attempted on a guardian in an
 * incompatible status (e.g. linking or updating an archived guardian).
 */
final class GuardianStateException extends RuntimeException
{
    public static function transition(GuardianStatus $from, string $target): self
    {
        return new self(
            "A guardian cannot transition from {$from->value} to {$target}.",
        );
    }

    public static function immutable(int $guardianId): self
    {
        return new self("Guardian #{$guardianId} is archived and cannot be modified.");
    }

    public static function message(string $message): self
    {
        return new self($message);
    }
}
