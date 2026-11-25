<?php

declare(strict_types=1);

namespace App\Domains\Role\DTOs;

use App\Domains\Role\Enums\RoleChangeOperation;

final readonly class RoleChangeCommand
{
    /**
     * @param  list<int>  $roleIds
     */
    public array $roleIds;

    /**
     * @param  array<int, int|string>  $roleIds
     */
    public function __construct(
        public string $targetUserId,
        array $roleIds,
        public ?string $schoolId,
        public RoleChangeOperation $operation,
    ) {
        $this->roleIds = array_values(array_unique(array_map(
            static fn (int|string $roleId): int => (int) $roleId,
            $roleIds,
        )));
    }
}
