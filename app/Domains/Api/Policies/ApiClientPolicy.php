<?php

declare(strict_types=1);

namespace App\Domains\Api\Policies;

use App\Models\ApiClient;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ApiClientPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('api.view');
    }

    public function view(User $user, ApiClient $model): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $model->school_id && $user->hasPermissionTo('api.view');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('api.create');
    }

    public function update(User $user, ApiClient $model): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $model->school_id && $user->hasPermissionTo('api.update');
    }

    public function delete(User $user, ApiClient $model): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $model->school_id && $user->hasPermissionTo('api.delete');
    }

    public function restore(User $user, ApiClient $model): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $model->school_id && $user->hasPermissionTo('api.delete');
    }

    public function forceDelete(User $user, ApiClient $model): bool
    {
        return false;
    }
}
