<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Domain\Exceptions;

use App\Domains\Assessment\Domain\Enums\AssessmentStatus;

/**
 * Thrown when an attempt is made to move an assessment to a status the
 * lifecycle does not allow from its current one (SPR-009).
 */
final class InvalidStatusTransitionException extends AssessmentException
{
    public static function from(AssessmentStatus $from, AssessmentStatus $to): self
    {
        return new self("Cannot transition assessment status from [{$from->value}] to [{$to->value}].");
    }
}
