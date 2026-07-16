<?php

namespace App\Domains\Department\Policies;

use App\Models\User;

class DepartmentPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, mixed $department): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'school_admin']);
    }

    public function update(User $user, mixed $department): bool
    {
        return $user->hasAnyRole(['super_admin', 'school_admin']);
    }

    public function delete(User $user, mixed $department): bool
    {
        return $user->hasRole('super_admin');
    }

    public function restore(User $user, mixed $department): bool
    {
        return false;
    }

    public function forceDelete(User $user, mixed $department): bool
    {
        return false;
    }
}
