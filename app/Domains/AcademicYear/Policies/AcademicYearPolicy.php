<?php

namespace App\Domains\AcademicYear\Policies;

use App\Models\User;

class AcademicYearPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, mixed $academicYear): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'school_admin']);
    }

    public function update(User $user, mixed $academicYear): bool
    {
        return $user->hasAnyRole(['super_admin', 'school_admin']);
    }

    public function delete(User $user, mixed $academicYear): bool
    {
        return $user->hasRole('super_admin');
    }

    public function activate(User $user, mixed $academicYear): bool
    {
        return $user->hasAnyRole(['super_admin', 'school_admin']);
    }

    public function restore(User $user, mixed $academicYear): bool
    {
        return false;
    }

    public function forceDelete(User $user, mixed $academicYear): bool
    {
        return false;
    }
}
