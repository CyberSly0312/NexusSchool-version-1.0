<?php

declare(strict_types=1);

namespace App\Domains\ReportCard\Domain\Exceptions;

use App\Domains\ReportCard\Domain\Enums\ReportCardStatus;

/**
 * Thrown when an attempt is made to move a report card run to a status the
 * lifecycle does not allow from its current one (SPR-011).
 */
final class InvalidStatusTransitionException extends ReportCardException
{
    public static function from(ReportCardStatus $from, ReportCardStatus $to): self
    {
        return new self("Cannot transition report card status from [{$from->value}] to [{$to->value}].");
    }
}
