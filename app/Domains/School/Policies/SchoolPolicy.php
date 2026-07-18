<?php

declare(strict_types=1);

namespace App\Domains\School\Policies;

use App\Domains\School\Models\School;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SchoolPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('school.view');
    }

    public function view(User $user, School $school): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $school->id && $user->hasPermissionTo('school.view');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('school.create');
    }

    public function update(User $user, School $school): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $school->id && $user->hasPermissionTo('school.update');
    }

    public function delete(User $user, School $school): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $school->id && $user->hasPermissionTo('school.delete');
    }

    public function restore(User $user, School $school): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $school->id && $user->hasPermissionTo('school.delete');
    }

    public function forceDelete(User $user, School $school): bool
    {
        return false;
    }
}
