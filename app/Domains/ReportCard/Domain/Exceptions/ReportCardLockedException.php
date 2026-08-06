<?php

declare(strict_types=1);

namespace App\Domains\ReportCard\Domain\Exceptions;

use App\Domains\ReportCard\Domain\Enums\ReportCardStatus;

/**
 * Thrown when an operation requires an open report card run but the run is
 * published or archived (SPR-011).
 */
final class ReportCardLockedException extends ReportCardException
{
    public static function immutable(ReportCardStatus $status): self
    {
        return new self("Report card run is [{$status->value}] and no longer editable.");
    }
}
