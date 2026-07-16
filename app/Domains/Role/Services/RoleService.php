<?php

namespace App\Domains\Role\Services;

use App\Domains\Role\DTOs\RoleData;
use App\Domains\Role\Events\RoleAssignedToUser;
use App\Domains\Role\Events\RoleCreated;
use App\Domains\Role\Events\RoleRevokedFromUser;
use App\Domains\Role\Events\RoleUpdated;
use App\Domains\Role\Rules\RoleRules;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class RoleService
{
    public function __construct(
        protected Role $model = new Role,
    ) {}

    /**
     * System roles that cannot be deleted or renamed.
     */
    public const SYSTEM_ROLES = [
        'super_admin',
        'school_admin',
        'director',
        'vice_director',
        'secretary',
        'accountant',
        'teacher',
        'librarian',
        'registrar',
        'student',
        'parent',
    ];

    public function create(RoleData $data): Role
    {
        return DB::transaction(function () use ($data) {
            $role = $this->model->create($data->toArray());

            RoleCreated::dispatch($role);

            return $role;
        });
    }

    public function update(Role $role, array $data): Role
    {
        $validated = RoleRules::validate($data, 'update', $role->id);

        $role->update($validated);

        RoleUpdated::dispatch($role);

        return $role->refresh();
    }

    public function delete(Role $role): bool
    {
        if ($this->isSystemRole($role)) {
            return false;
        }

        $role->delete();

        return true;
    }

    public function assignToUser(User $user, string|Role $role): void
    {
        $roleName = $role instanceof Role ? $role->name : $role;
        $roleModel = $role instanceof Role ? $role : $this->findByName($roleName);

        $user->assignRole($roleModel);

        RoleAssignedToUser::dispatch($user, $roleModel);
    }

    public function revokeFromUser(User $user, string|Role $role): void
    {
        $roleName = $role instanceof Role ? $role->name : $role;
        $roleModel = $role instanceof Role ? $role : $this->findByName($roleName);

        $user->removeRole($roleModel);

        RoleRevokedFromUser::dispatch($user, $roleModel);
    }

    public function syncUserRoles(User $user, array $roleIds): void
    {
        $roleNames = Role::whereIn('id', $roleIds)->pluck('name')->toArray();
        $user->syncRoles($roleNames);
    }

    public function findByName(string $name): Role
    {
        return $this->model->where('name', $name)->firstOrFail();
    }

    public function findById(int|string $id): Role
    {
        return $this->model->findOrFail($id);
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where('name', 'like', "%{$search}%");
        }

        return $query->orderBy('name')->paginate($perPage);
    }

    public function isSystemRole(Role $role): bool
    {
        return in_array($role->name, self::SYSTEM_ROLES);
    }

    public function getDefaultRoles(): array
    {
        return collect(self::SYSTEM_ROLES)->map(fn (string $name) => [
            'name' => $name,
            'guard_name' => 'web',
        ])->toArray();
    }
}
