<?php

declare(strict_types=1);

namespace App\Domains\Department\Policies;

use App\Models\Department;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DepartmentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('department.view');
    }

    public function view(User $user, Department $department): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $department->school_id && $user->hasPermissionTo('department.view');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('department.create');
    }

    public function update(User $user, Department $department): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $department->school_id && $user->hasPermissionTo('department.update');
    }

    public function delete(User $user, Department $department): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $department->school_id && $user->hasPermissionTo('department.delete');
    }

    public function restore(User $user, Department $department): bool
    {
        return false;
    }

    public function forceDelete(User $user, Department $department): bool
    {
        return false;
    }
}
