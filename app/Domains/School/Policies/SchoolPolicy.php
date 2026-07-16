<?php

namespace App\Domains\School\Policies;

use App\Domains\School\Models\School;
use App\Models\User;

class SchoolPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('school.view');
    }

    public function view(User $user, School $school): bool
    {
        return $user->hasPermissionTo('school.view')
            && $user->school_id === $school->id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('school.create');
    }

    public function update(User $user, School $school): bool
    {
        return $user->hasPermissionTo('school.update')
            && $user->school_id === $school->id;
    }

    public function delete(User $user, School $school): bool
    {
        return $user->hasPermissionTo('school.delete')
            && $user->school_id === $school->id;
    }

    public function restore(User $user, School $school): bool
    {
        return $user->hasPermissionTo('school.delete')
            && $user->school_id === $school->id;
    }

    public function forceDelete(User $user, School $school): bool
    {
        return false;
    }
}
