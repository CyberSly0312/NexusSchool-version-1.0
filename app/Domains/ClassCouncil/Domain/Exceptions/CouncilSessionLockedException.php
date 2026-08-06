<?php

declare(strict_types=1);

namespace App\Domains\ClassCouncil\Domain\Exceptions;

use App\Domains\ClassCouncil\Domain\Enums\CouncilSessionStatus;

/**
 * Thrown when an operation requires an open, deliberating council session but
 * the session is not convened (SPR-013).
 */
final class CouncilSessionLockedException extends ClassCouncilException
{
    public static function notDeliberating(CouncilSessionStatus $status): self
    {
        return new self("Council session is [{$status->value}] and no longer accepts decisions.");
    }
}
