<?php

declare(strict_types=1);

namespace App\Domains\System\Policies;

use App\Models\Backup;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BackupPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('backup.view');
    }

    public function view(User $user, Backup $model): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $model->school_id && $user->hasPermissionTo('backup.view');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('backup.create');
    }

    public function update(User $user, Backup $model): bool
    {
        return false;
    }

    public function delete(User $user, Backup $model): bool
    {
        return $user->hasRole('super_admin');
    }

    public function restore(User $user, Backup $model): bool
    {
        return $user->hasRole('super_admin');
    }
}
