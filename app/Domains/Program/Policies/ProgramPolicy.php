<?php

declare(strict_types=1);

namespace App\Domains\Program\Policies;

use App\Models\Program;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProgramPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('program.view');
    }

    public function view(User $user, Program $program): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $program->school_id && $user->hasPermissionTo('program.view');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('program.create');
    }

    public function update(User $user, Program $program): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $program->school_id && $user->hasPermissionTo('program.update');
    }

    public function delete(User $user, Program $program): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $program->school_id && $user->hasPermissionTo('program.delete');
    }

    public function restore(User $user, Program $program): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $program->school_id && $user->hasPermissionTo('program.delete');
    }

    public function forceDelete(User $user, Program $program): bool
    {
        return false;
    }
}
