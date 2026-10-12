<?php

declare(strict_types=1);

namespace App\Domains\IdentityAndVerification\Infrastructure\Policies;

use App\Domains\IdentityAndVerification\Infrastructure\Models\VerificationIdentity;
use App\Models\User;

/**
 * Authorization for verification identity lifecycle transitions (SPR-016
 * Phase 4, ADR-022 Decision 4 / I13).
 *
 * Both revocation and replacement are governed by the single
 * document.revoke capability (ADR-022 names exactly one invalidation
 * capability; no separate document.replace permission exists). The super_admin
 * role is the platform-wide escape hatch shared by every domain policy, and
 * every grant is school-scoped: a user of one school can never invalidate
 * another school's identity.
 */
final class VerificationIdentityPolicy
{
    public function revoke(User $user, VerificationIdentity $identity): bool
    {
        return $this->authorizeLifecycle($user, $identity);
    }

    public function replace(User $user, VerificationIdentity $identity): bool
    {
        return $this->authorizeLifecycle($user, $identity);
    }

    private function authorizeLifecycle(User $user, VerificationIdentity $identity): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $identity->school_id && $user->hasPermissionTo('document.revoke');
    }
}
