<?php

namespace App\Domains\Permission\Policies;

use App\Models\User;

class PermissionPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, mixed $permission): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin');
    }

    public function update(User $user, mixed $permission): bool
    {
        return $user->hasRole('super_admin');
    }

    public function delete(User $user, mixed $permission): bool
    {
        return $user->hasRole('super_admin');
    }

    public function restore(User $user, mixed $permission): bool
    {
        return false;
    }

    public function forceDelete(User $user, mixed $permission): bool
    {
        return false;
    }
}
