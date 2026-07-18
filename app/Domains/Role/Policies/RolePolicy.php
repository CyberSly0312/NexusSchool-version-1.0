<?php

declare(strict_types=1);

namespace App\Domains\Role\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Spatie\Permission\Models\Role;

class RolePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('role.view');
    }

    public function view(User $user, Role $role): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->hasPermissionTo('role.view');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('role.create');
    }

    public function update(User $user, Role $role): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->hasPermissionTo('role.update');
    }

    public function delete(User $user, Role $role): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->hasPermissionTo('role.delete');
    }

    public function restore(User $user, Role $role): bool
    {
        return false;
    }

    public function forceDelete(User $user, Role $role): bool
    {
        return false;
    }
}
