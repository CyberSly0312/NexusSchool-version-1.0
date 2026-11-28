<?php

declare(strict_types=1);

namespace App\Domains\Guardian\Domain\Exceptions;

use App\Domains\Guardian\Domain\Enums\GuardianAccessDenialReason;
use RuntimeException;

/**
 * Raised when a Parent surface requests student data that is not explicitly,
 * currently and school-scoped authorized for the authenticated Guardian.
 */
final class GuardianChildAccessDeniedException extends RuntimeException
{
    public function __construct(
        public readonly GuardianAccessDenialReason $reason,
    ) {
        parent::__construct('Guardian child access denied: '.$reason->value);
    }

    public static function because(GuardianAccessDenialReason $reason): self
    {
        return new self($reason);
    }
}
