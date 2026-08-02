<?php

declare(strict_types=1);

namespace App\Domains\Academic\Domain\Exceptions;

use RuntimeException;

/**
 * Thrown when an invariant-backed uniqueness constraint is violated at the
 * application level (the database unique index is the final line of defense).
 */
final class DuplicateResourceException extends RuntimeException
{
    public static function for(string $resource, string $field, string $value): self
    {
        return new self("A [{$resource}] with {$field} [{$value}] already exists.");
    }
}
