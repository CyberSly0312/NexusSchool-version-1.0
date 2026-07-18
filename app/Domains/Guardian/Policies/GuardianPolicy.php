<?php

declare(strict_types=1);

namespace App\Domains\Guardian\Policies;

use App\Models\Guardian;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class GuardianPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('guardian.view');
    }

    public function view(User $user, Guardian $guardian): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $guardian->school_id && $user->hasPermissionTo('guardian.view');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('guardian.create');
    }

    public function update(User $user, Guardian $guardian): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $guardian->school_id && $user->hasPermissionTo('guardian.update');
    }

    public function delete(User $user, Guardian $guardian): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $guardian->school_id && $user->hasPermissionTo('guardian.delete');
    }

    public function restore(User $user, Guardian $guardian): bool
    {
        return false;
    }

    public function forceDelete(User $user, Guardian $guardian): bool
    {
        return false;
    }
}
