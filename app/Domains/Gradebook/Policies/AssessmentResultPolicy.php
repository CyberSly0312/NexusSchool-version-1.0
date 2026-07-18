<?php

declare(strict_types=1);

namespace App\Domains\Gradebook\Policies;

use App\Models\AssessmentResult;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AssessmentResultPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('gradebook.view');
    }

    public function view(User $user, AssessmentResult $result): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $result->school_id && $user->hasPermissionTo('gradebook.view');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('gradebook.create');
    }

    public function update(User $user, AssessmentResult $result): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $result->school_id && $user->hasPermissionTo('gradebook.update');
    }

    public function delete(User $user, AssessmentResult $result): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $result->school_id && $user->hasPermissionTo('gradebook.delete');
    }

    public function restore(User $user, AssessmentResult $result): bool
    {
        return false;
    }

    public function forceDelete(User $user, AssessmentResult $result): bool
    {
        return false;
    }
}
