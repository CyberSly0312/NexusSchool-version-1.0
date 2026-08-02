<?php

declare(strict_types=1);

namespace App\Domains\Academic\Domain\Exceptions;

use RuntimeException;

/**
 * Thrown when deletion is attempted on an entity that still has dependants.
 */
final class EntityInUseException extends RuntimeException
{
    public static function for(string $resource, string $dependants): self
    {
        return new self("Cannot delete [{$resource}]: it is still referenced by {$dependants}.");
    }
}
