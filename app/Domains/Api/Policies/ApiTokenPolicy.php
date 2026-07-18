<?php

declare(strict_types=1);

namespace App\Domains\Api\Policies;

use App\Models\ApiToken;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ApiTokenPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('api.view');
    }

    public function view(User $user, ApiToken $model): bool
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

    public function delete(User $user, ApiToken $model): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $model->school_id && $user->hasPermissionTo('api.delete');
    }
}
