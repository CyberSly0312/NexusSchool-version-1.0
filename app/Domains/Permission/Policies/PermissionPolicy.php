<?php

declare(strict_types=1);

namespace App\Domains\Permission\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Spatie\Permission\Models\Permission;

class PermissionPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('permission.view');
    }

    public function view(User $user, Permission $permission): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->hasPermissionTo('permission.view');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('permission.create');
    }

    public function update(User $user, Permission $permission): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->hasPermissionTo('permission.update');
    }

    public function delete(User $user, Permission $permission): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->hasPermissionTo('permission.delete');
    }

    public function restore(User $user, Permission $permission): bool
    {
        return false;
    }

    public function forceDelete(User $user, Permission $permission): bool
    {
        return false;
    }
}
