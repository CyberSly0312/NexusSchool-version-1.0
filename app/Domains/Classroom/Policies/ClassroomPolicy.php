<?php

declare(strict_types=1);

namespace App\Domains\Classroom\Policies;

use App\Models\Classroom;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ClassroomPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('classroom.view');
    }

    public function view(User $user, Classroom $classroom): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $classroom->school_id && $user->hasPermissionTo('classroom.view');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('classroom.create');
    }

    public function update(User $user, Classroom $classroom): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $classroom->school_id && $user->hasPermissionTo('classroom.update');
    }

    public function delete(User $user, Classroom $classroom): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $classroom->school_id && $user->hasPermissionTo('classroom.delete');
    }

    public function restore(User $user, Classroom $classroom): bool
    {
        return false;
    }

    public function forceDelete(User $user, Classroom $classroom): bool
    {
        return false;
    }
}
