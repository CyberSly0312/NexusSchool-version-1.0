<?php

declare(strict_types=1);

namespace App\Domains\Guardian\Domain\Enums;

/**
 * Verification state of a durable Guardian–Student relationship for portal use.
 *
 * This is deliberately separate from the existing responsibility flags: it only
 * records whether the school has independently verified the relationship for
 * account access. It does not create a new responsibility taxonomy.
 */
enum RelationshipVerificationStatus: string
{
    case Unverified = 'unverified';
    case Verified = 'verified';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Unverified => 'Non vérifiée',
            self::Verified => 'Vérifiée',
            self::Rejected => 'Rejetée',
        };
    }
}
