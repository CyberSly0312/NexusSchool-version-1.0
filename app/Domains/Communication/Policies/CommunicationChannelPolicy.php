<?php

declare(strict_types=1);

namespace App\Domains\Communication\Policies;

use App\Models\CommunicationChannel;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommunicationChannelPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('communication.view');
    }

    public function view(User $user, CommunicationChannel $model): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $model->school_id && $user->hasPermissionTo('communication.view');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('communication.create');
    }

    public function update(User $user, CommunicationChannel $model): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $model->school_id && $user->hasPermissionTo('communication.update');
    }

    public function delete(User $user, CommunicationChannel $model): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $model->school_id && $user->hasPermissionTo('communication.delete');
    }
}
