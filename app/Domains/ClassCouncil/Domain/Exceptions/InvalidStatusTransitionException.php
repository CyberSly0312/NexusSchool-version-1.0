<?php

declare(strict_types=1);

namespace App\Domains\ClassCouncil\Domain\Exceptions;

use App\Domains\ClassCouncil\Domain\Enums\CouncilSessionStatus;

/**
 * Thrown when an attempt is made to move a council session to a status the
 * lifecycle does not allow from its current one (SPR-013).
 */
final class InvalidStatusTransitionException extends ClassCouncilException
{
    public static function from(CouncilSessionStatus $from, CouncilSessionStatus $to): self
    {
        return new self("Cannot transition council session status from [{$from->value}] to [{$to->value}].");
    }
}
