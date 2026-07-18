<?php

declare(strict_types=1);

namespace App\Domains\AcademicYear\Policies;

use App\Models\AcademicYear;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AcademicYearPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('academic_year.view');
    }

    public function view(User $user, AcademicYear $academicYear): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $academicYear->school_id && $user->hasPermissionTo('academic_year.view');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('academic_year.create');
    }

    public function update(User $user, AcademicYear $academicYear): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $academicYear->school_id && $user->hasPermissionTo('academic_year.update');
    }

    public function delete(User $user, AcademicYear $academicYear): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $academicYear->school_id && $user->hasPermissionTo('academic_year.delete');
    }

    public function activate(User $user, AcademicYear $academicYear): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $academicYear->school_id && $user->hasPermissionTo('academic_year.update');
    }

    public function restore(User $user, AcademicYear $academicYear): bool
    {
        return false;
    }

    public function forceDelete(User $user, AcademicYear $academicYear): bool
    {
        return false;
    }
}
