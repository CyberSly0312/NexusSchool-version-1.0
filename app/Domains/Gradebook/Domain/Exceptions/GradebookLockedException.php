<?php

declare(strict_types=1);

namespace App\Domains\Gradebook\Domain\Exceptions;

/**
 * Thrown when an actor attempts to mutate gradebook data while the gradebook
 * is locked (SPR-010). Locked entries are immutable; a re-opening requires an
 * explicit, authorized Unlock.
 */
final class GradebookLockedException extends GradebookException
{
    public static function for(int|string $gradebookId): self
    {
        return new self("Gradebook [{$gradebookId}] is locked; its entries cannot be mutated.");
    }
}
