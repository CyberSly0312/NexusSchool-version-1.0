<?php

declare(strict_types=1);

namespace App\Domains\GradeLevel\Policies;

use App\Models\GradeLevel;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class GradeLevelPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('grade_level.view');
    }

    public function view(User $user, GradeLevel $gradeLevel): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $gradeLevel->school_id && $user->hasPermissionTo('grade_level.view');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('grade_level.create');
    }

    public function update(User $user, GradeLevel $gradeLevel): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $gradeLevel->school_id && $user->hasPermissionTo('grade_level.update');
    }

    public function delete(User $user, GradeLevel $gradeLevel): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $gradeLevel->school_id && $user->hasPermissionTo('grade_level.delete');
    }

    public function restore(User $user, GradeLevel $gradeLevel): bool
    {
        return false;
    }

    public function forceDelete(User $user, GradeLevel $gradeLevel): bool
    {
        return false;
    }
}
