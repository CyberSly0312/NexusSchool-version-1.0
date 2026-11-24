<?php

declare(strict_types=1);

namespace App\Domains\User\DTOs;

use App\Models\User;
use InvalidArgumentException;

final readonly class AccountProvisioningContext
{
    private function __construct(
        public readonly User $actor,
        private readonly bool $platformProvisioning,
    ) {
    }

    public static function forPlatform(User $actor): self
    {
        if (! $actor->hasRole('super_admin')) {
            throw new InvalidArgumentException('Platform provisioning requires a super_admin actor.');
        }

        return new self($actor, true);
    }

    public static function forSchoolAdmin(User $actor): self
    {
        if (! $actor->hasRole('school_admin')) {
            throw new InvalidArgumentException('School-level provisioning requires a school_admin actor.');
        }

        return new self($actor, false);
    }

    public function isPlatformProvisioning(): bool
    {
        return $this->platformProvisioning;
    }

    public function isSchoolAdminProvisioning(): bool
    {
        return ! $this->platformProvisioning;
    }
}
