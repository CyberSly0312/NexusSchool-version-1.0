<?php

declare(strict_types=1);

namespace App\Domains\Role\DTOs;

use App\Domains\School\Models\School;
use App\Models\User;
use Spatie\Permission\Models\Role;

final readonly class RoleChangePlan
{
    /**
     * @param  list<Role>  $roles
     */
    public function __construct(
        public User $target,
        public array $roles,
        public ?School $school,
        public ?string $schoolId,
        public RoleChangeContext $context,
    ) {
    }
}
