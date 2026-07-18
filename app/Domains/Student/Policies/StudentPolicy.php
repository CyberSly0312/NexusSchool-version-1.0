<?php

declare(strict_types=1);

namespace App\Domains\Student\Policies;

use App\Models\Student;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StudentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('student.view');
    }

    public function view(User $user, Student $student): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $student->school_id && $user->hasPermissionTo('student.view');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('student.create');
    }

    public function update(User $user, Student $student): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $student->school_id && $user->hasPermissionTo('student.update');
    }

    public function delete(User $user, Student $student): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $student->school_id && $user->hasPermissionTo('student.delete');
    }

    public function restore(User $user, Student $student): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $student->school_id && $user->hasPermissionTo('student.delete');
    }

    public function forceDelete(User $user, Student $student): bool
    {
        return false;
    }
}
