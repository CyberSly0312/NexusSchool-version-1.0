<?php

declare(strict_types=1);

namespace App\Domains\Guardian\Domain\Exceptions;

use RuntimeException;

/**
 * Raised when an operation is attempted on an unlinked (soft-deleted) or
 * otherwise inactive student–guardian relationship.
 */
final class RelationshipStateException extends RuntimeException
{
    public static function unlinked(int $relationshipId): self
    {
        return new self("Relationship #{$relationshipId} has been unlinked and cannot be modified.");
    }

    public static function message(string $message): self
    {
        return new self($message);
    }
}
