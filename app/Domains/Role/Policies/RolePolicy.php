<?php

namespace App\Domains\Role\Policies;

use App\Models\User;

class RolePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, mixed $role): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin');
    }

    public function update(User $user, mixed $role): bool
    {
        return $user->hasRole('super_admin');
    }

    public function delete(User $user, mixed $role): bool
    {
        return $user->hasRole('super_admin');
    }

    public function restore(User $user, mixed $role): bool
    {
        return false;
    }

    public function forceDelete(User $user, mixed $role): bool
    {
        return false;
    }
}
