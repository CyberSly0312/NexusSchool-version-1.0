<?php

declare(strict_types=1);

namespace App\Domains\System\Policies;

use App\Models\MaintenanceWindow;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MaintenanceWindowPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('maintenance_window.view');
    }

    public function view(User $user, MaintenanceWindow $model): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $model->school_id && $user->hasPermissionTo('maintenance_window.view');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('maintenance_window.create');
    }

    public function update(User $user, MaintenanceWindow $model): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $model->school_id && $user->hasPermissionTo('maintenance_window.update');
    }

    public function delete(User $user, MaintenanceWindow $model): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $model->school_id && $user->hasPermissionTo('maintenance_window.delete');
    }
}
