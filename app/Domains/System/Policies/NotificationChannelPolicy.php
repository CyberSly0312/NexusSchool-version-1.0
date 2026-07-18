<?php

declare(strict_types=1);

namespace App\Domains\System\Policies;

use App\Models\NotificationChannel;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class NotificationChannelPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('notification_channel.view');
    }

    public function view(User $user, NotificationChannel $model): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $model->school_id && $user->hasPermissionTo('notification_channel.view');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('notification_channel.create');
    }

    public function update(User $user, NotificationChannel $model): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $model->school_id && $user->hasPermissionTo('notification_channel.update');
    }

    public function delete(User $user, NotificationChannel $model): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $model->school_id && $user->hasPermissionTo('notification_channel.delete');
    }
}
