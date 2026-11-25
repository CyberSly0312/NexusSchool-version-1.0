<?php

declare(strict_types=1);

namespace App\Domains\Role\DTOs;

use App\Domains\Role\Enums\RoleChangeOperation;
use App\Models\User;
use Spatie\Permission\Models\Role;

final readonly class RoleChangeResult
{
    /**
     * @param  list<Role>  $roles
     */
    public function __construct(
        public User $target,
        public array $roles,
        public ?string $schoolId,
        public RoleChangeOperation $operation,
        public bool $changed,
    ) {
    }
}
