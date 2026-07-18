<?php

declare(strict_types=1);

namespace App\Domains\Transport\Policies;

use App\Models\Driver;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DriverPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('transport.view');
    }

    public function view(User $user, Driver $model): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $model->school_id && $user->hasPermissionTo('transport.view');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('transport.create');
    }

    public function update(User $user, Driver $model): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $model->school_id && $user->hasPermissionTo('transport.update');
    }

    public function delete(User $user, Driver $model): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $model->school_id && $user->hasPermissionTo('transport.delete');
    }

    public function restore(User $user, Driver $model): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $model->school_id && $user->hasPermissionTo('transport.delete');
    }

    public function forceDelete(User $user, Driver $model): bool
    {
        return false;
    }
}
