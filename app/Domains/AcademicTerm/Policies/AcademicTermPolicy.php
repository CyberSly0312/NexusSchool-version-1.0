<?php

declare(strict_types=1);

namespace App\Domains\AcademicTerm\Policies;

use App\Models\AcademicTerm;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AcademicTermPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('academic_term.view');
    }

    public function view(User $user, AcademicTerm $academicTerm): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $academicTerm->school_id && $user->hasPermissionTo('academic_term.view');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('academic_term.create');
    }

    public function update(User $user, AcademicTerm $academicTerm): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $academicTerm->school_id && $user->hasPermissionTo('academic_term.update');
    }

    public function delete(User $user, AcademicTerm $academicTerm): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $academicTerm->school_id && $user->hasPermissionTo('academic_term.delete');
    }

    public function activate(User $user, AcademicTerm $academicTerm): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $academicTerm->school_id && $user->hasPermissionTo('academic_term.update');
    }

    public function restore(User $user, AcademicTerm $academicTerm): bool
    {
        return false;
    }

    public function forceDelete(User $user, AcademicTerm $academicTerm): bool
    {
        return false;
    }
}
