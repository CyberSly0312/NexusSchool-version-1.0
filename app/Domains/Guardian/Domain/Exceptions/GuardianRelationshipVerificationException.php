<?php

declare(strict_types=1);

namespace App\Domains\Guardian\Domain\Exceptions;

use App\Domains\Guardian\Domain\Enums\GuardianAccessDenialReason;
use RuntimeException;

/**
 * Raised when a Guardian–Student relationship cannot be verified or authorized
 * under the approved Task 11.3 rules.
 */
final class GuardianRelationshipVerificationException extends RuntimeException
{
    public function __construct(
        public readonly GuardianAccessDenialReason $reason,
    ) {
        parent::__construct('Guardian relationship verification denied: '.$reason->value);
    }

    public static function because(GuardianAccessDenialReason $reason): self
    {
        return new self($reason);
    }
}
