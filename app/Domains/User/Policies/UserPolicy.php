<?php

namespace App\Domains\User\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('user.view');
    }

    public function view(User $user, User $model): bool
    {
        return $user->hasPermissionTo('user.view')
            && $user->school_id === $model->school_id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('user.create');
    }

    public function update(User $user, User $model): bool
    {
        return $user->hasPermissionTo('user.update')
            && $user->school_id === $model->school_id;
    }

    public function delete(User $user, User $model): bool
    {
        return $user->hasPermissionTo('user.delete')
            && $user->school_id === $model->school_id;
    }

    public function restore(User $user, User $model): bool
    {
        return $user->hasPermissionTo('user.delete')
            && $user->school_id === $model->school_id;
    }

    public function forceDelete(User $user, User $model): bool
    {
        return false;
    }
}
