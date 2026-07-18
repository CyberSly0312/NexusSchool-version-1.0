<?php

declare(strict_types=1);

namespace App\Domains\Analytics\Policies;

use App\Models\Dashboard;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DashboardPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('analytics.view');
    }

    public function view(User $user, Dashboard $model): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $model->school_id && $user->hasPermissionTo('analytics.view');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('analytics.create');
    }

    public function update(User $user, Dashboard $model): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $model->school_id && $user->hasPermissionTo('analytics.update');
    }

    public function delete(User $user, Dashboard $model): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $model->school_id && $user->hasPermissionTo('analytics.delete');
    }

    public function restore(User $user, Dashboard $model): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $model->school_id && $user->hasPermissionTo('analytics.delete');
    }

    public function forceDelete(User $user, Dashboard $model): bool
    {
        return false;
    }
}
