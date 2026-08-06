<?php

declare(strict_types=1);

namespace App\Domains\Gradebook\Domain\Exceptions;

use App\Domains\Gradebook\Domain\Enums\GradebookStatus;

/**
 * Thrown when an attempt is made to move a gradebook to a status the lifecycle
 * does not allow from its current one (SPR-010).
 */
final class InvalidStatusTransitionException extends GradebookException
{
    public static function from(GradebookStatus $from, GradebookStatus $to): self
    {
        return new self("Cannot transition gradebook status from [{$from->value}] to [{$to->value}].");
    }
}
