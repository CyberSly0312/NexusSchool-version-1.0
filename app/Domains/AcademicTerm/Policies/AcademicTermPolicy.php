<?php

namespace App\Domains\AcademicTerm\Policies;

use App\Models\User;

class AcademicTermPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, mixed $academicTerm): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'school_admin']);
    }

    public function update(User $user, mixed $academicTerm): bool
    {
        return $user->hasAnyRole(['super_admin', 'school_admin']);
    }

    public function delete(User $user, mixed $academicTerm): bool
    {
        return $user->hasRole('super_admin');
    }

    public function activate(User $user, mixed $academicTerm): bool
    {
        return $user->hasAnyRole(['super_admin', 'school_admin']);
    }

    public function restore(User $user, mixed $academicTerm): bool
    {
        return false;
    }

    public function forceDelete(User $user, mixed $academicTerm): bool
    {
        return false;
    }
}
