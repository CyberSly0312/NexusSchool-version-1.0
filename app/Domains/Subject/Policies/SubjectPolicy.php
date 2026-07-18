<?php

declare(strict_types=1);

namespace App\Domains\Subject\Policies;

use App\Models\Subject;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SubjectPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('subject.view');
    }

    public function view(User $user, Subject $subject): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $subject->school_id && $user->hasPermissionTo('subject.view');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('subject.create');
    }

    public function update(User $user, Subject $subject): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $subject->school_id && $user->hasPermissionTo('subject.update');
    }

    public function delete(User $user, Subject $subject): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $subject->school_id && $user->hasPermissionTo('subject.delete');
    }

    public function restore(User $user, Subject $subject): bool
    {
        return false;
    }

    public function forceDelete(User $user, Subject $subject): bool
    {
        return false;
    }
}
