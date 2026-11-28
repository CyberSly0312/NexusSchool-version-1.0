<?php

declare(strict_types=1);

namespace App\Domains\Guardian\Domain\Enums;

/**
 * Explicit portal authorization state of a Guardian–Student relationship.
 *
 * Access is granted only when the verification state is `verified`, this state
 * is `authorized`, and the relationship dates are currently effective.
 */
enum RelationshipPortalAccessStatus: string
{
    case NotAuthorized = 'not_authorized';
    case Authorized = 'authorized';
    case Revoked = 'revoked';

    public function label(): string
    {
        return match ($this) {
            self::NotAuthorized => 'Non autorisé',
            self::Authorized => 'Autorisé',
            self::Revoked => 'Révoqué',
        };
    }
}
