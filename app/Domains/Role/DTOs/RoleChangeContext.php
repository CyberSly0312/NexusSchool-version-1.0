<?php

declare(strict_types=1);

namespace App\Domains\Role\DTOs;

use App\Domains\Role\Exceptions\RoleChangeException;
use App\Models\User;

final readonly class RoleChangeContext
{
    private function __construct(
        public User $actor,
        public bool $platformOperation,
        public ?string $tenantSchoolId,
    ) {
    }

    public static function forActor(User $actor, ?string $tenantSchoolId = null): self
    {
        if ($actor->trashed() || ! $actor->isActive()) {
            throw new RoleChangeException(
                'Seul un administrateur actif peut modifier des rôles.',
                RoleChangeException::REASON_ACTOR,
            );
        }

        $tenantSchoolId = $tenantSchoolId !== null && trim($tenantSchoolId) !== ''
            ? trim($tenantSchoolId)
            : null;

        if ($actor->hasRole('super_admin')) {
            return new self($actor, true, $tenantSchoolId);
        }

        if (! $actor->hasRole('school_admin')) {
            throw new RoleChangeException(
                'Vous n’êtes pas autorisé à modifier des rôles.',
                RoleChangeException::REASON_ACTOR,
            );
        }

        $actorSchoolId = (string) $actor->school_id;

        if ($tenantSchoolId === null || $actorSchoolId === '' || $actorSchoolId !== $tenantSchoolId) {
            throw new RoleChangeException(
                'Le contexte d’établissement requis n’est pas disponible.',
                RoleChangeException::REASON_SCHOOL,
            );
        }

        return new self($actor, false, $tenantSchoolId);
    }

    public function isPlatformOperation(): bool
    {
        return $this->platformOperation;
    }

    public function isSchoolAdminOperation(): bool
    {
        return ! $this->platformOperation;
    }
}
