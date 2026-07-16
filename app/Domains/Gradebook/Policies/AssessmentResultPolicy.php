<?php

namespace App\Domains\Gradebook\Policies;

use App\Models\User;

class AssessmentResultPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, mixed $result): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'school_admin', 'teacher']);
    }

    public function update(User $user, mixed $result): bool
    {
        return $user->hasAnyRole(['super_admin', 'school_admin', 'teacher']);
    }

    public function delete(User $user, mixed $result): bool
    {
        return $user->hasRole('super_admin');
    }

    public function restore(User $user, mixed $result): bool
    {
        return false;
    }

    public function forceDelete(User $user, mixed $result): bool
    {
        return false;
    }
}
