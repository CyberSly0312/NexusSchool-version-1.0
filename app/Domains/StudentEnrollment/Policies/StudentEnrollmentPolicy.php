<?php

declare(strict_types=1);

namespace App\Domains\StudentEnrollment\Policies;

use App\Models\StudentEnrollment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StudentEnrollmentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('student_enrollment.view');
    }

    public function view(User $user, StudentEnrollment $enrollment): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $enrollment->school_id && $user->hasPermissionTo('student_enrollment.view');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('student_enrollment.create');
    }

    public function update(User $user, StudentEnrollment $enrollment): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $enrollment->school_id && $user->hasPermissionTo('student_enrollment.update');
    }

    public function delete(User $user, StudentEnrollment $enrollment): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $enrollment->school_id && $user->hasPermissionTo('student_enrollment.delete');
    }

    public function restore(User $user, StudentEnrollment $enrollment): bool
    {
        return false;
    }

    public function forceDelete(User $user, StudentEnrollment $enrollment): bool
    {
        return false;
    }
}
