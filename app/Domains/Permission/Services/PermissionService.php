<?php

namespace App\Domains\Permission\Services;

use App\Domains\Permission\DTOs\PermissionData;
use App\Domains\Permission\Enums\PermissionGroup;
use App\Domains\Permission\Events\PermissionAssignedToRole;
use App\Domains\Permission\Events\PermissionCreated;
use App\Domains\Permission\Events\PermissionRevokedFromRole;
use App\Domains\Permission\Events\PermissionUpdated;
use App\Domains\Permission\Rules\PermissionRules;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionService
{
    public function __construct(
        protected Permission $model = new Permission,
    ) {}

    public const SYSTEM_PERMISSIONS = [
        'permission.view',
        'permission.create',
        'permission.update',
        'permission.delete',
    ];

    public const CRUD_ACTIONS = ['view', 'create', 'update', 'delete'];

    public function create(PermissionData $data): Permission
    {
        return DB::transaction(function () use ($data) {
            $permission = $this->model->create($data->toArray());

            PermissionCreated::dispatch($permission);

            return $permission;
        });
    }

    public function update(Permission $permission, array $data): Permission
    {
        $validated = PermissionRules::validate($data, 'update', $permission->id);

        $permission->update($validated);

        PermissionUpdated::dispatch($permission);

        return $permission->refresh();
    }

    public function delete(Permission $permission): bool
    {
        if ($this->isSystemPermission($permission)) {
            return false;
        }

        $permission->delete();

        return true;
    }

    public function assignToRole(Permission $permission, Role $role): void
    {
        $role->givePermissionTo($permission);

        PermissionAssignedToRole::dispatch($permission, $role);
    }

    public function revokeFromRole(Permission $permission, Role $role): void
    {
        $role->revokePermissionTo($permission);

        PermissionRevokedFromRole::dispatch($permission, $role);
    }

    public function syncRolePermissions(Role $role, array $permissionIds): void
    {
        $permissionNames = Permission::whereIn('id', $permissionIds)->pluck('name')->toArray();
        $role->syncPermissions($permissionNames);
    }

    public function findByName(string $name): Permission
    {
        return $this->model->where('name', $name)->firstOrFail();
    }

    public function findById(int|string $id): Permission
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

        if (isset($filters['group'])) {
            $group = $filters['group'];
            $query->where('name', 'like', "{$group}.%");
        }

        return $query->orderBy('name')->paginate($perPage);
    }

    public function isSystemPermission(Permission $permission): bool
    {
        return in_array($permission->name, self::SYSTEM_PERMISSIONS);
    }

    public function getByGroup(string $group): Collection
    {
        return $this->model->where('name', 'like', "{$group}.%")->get();
    }

    public function getGroupedPermissions(): array
    {
        $permissions = $this->model->orderBy('name')->get();

        $grouped = [];

        foreach ($permissions as $permission) {
            $groupName = $this->extractGroupName($permission->name);
            $grouped[$groupName][] = $permission;
        }

        return $grouped;
    }

    public function extractGroupName(string $permissionName): string
    {
        $parts = explode('.', $permissionName);

        return $parts[0] ?? $permissionName;
    }

    public function getDefaultPermissions(): array
    {
        $permissions = [];

        foreach (PermissionGroup::cases() as $group) {
            foreach (self::CRUD_ACTIONS as $action) {
                $permissions[] = [
                    'name' => "{$group->value}.{$action}",
                    'guard_name' => 'web',
                ];
            }
        }

        return $permissions;
    }
}
