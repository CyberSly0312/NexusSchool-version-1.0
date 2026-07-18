<?php

declare(strict_types=1);

namespace App\Domains\AcademicClass\Policies;

use App\Models\AcademicClass;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AcademicClassPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('academic_class.view');
    }

    public function view(User $user, AcademicClass $academicClass): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $academicClass->school_id && $user->hasPermissionTo('academic_class.view');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('academic_class.create');
    }

    public function update(User $user, AcademicClass $academicClass): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $academicClass->school_id && $user->hasPermissionTo('academic_class.update');
    }

    public function delete(User $user, AcademicClass $academicClass): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $academicClass->school_id && $user->hasPermissionTo('academic_class.delete');
    }

    public function restore(User $user, AcademicClass $academicClass): bool
    {
        return false;
    }

    public function forceDelete(User $user, AcademicClass $academicClass): bool
    {
        return false;
    }
}
