<?php

declare(strict_types=1);

namespace App\Domains\Academic\Domain\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

trait HandlesSchoolResourceAuthorization
{
    abstract protected function permissionGroup(): string;

    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo($this->permissionGroup().'.view');
    }

    public function view(User $user, Model $model): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $model->school_id && $user->hasPermissionTo($this->permissionGroup().'.view');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo($this->permissionGroup().'.create');
    }

    public function update(User $user, Model $model): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $model->school_id && $user->hasPermissionTo($this->permissionGroup().'.update');
    }

    public function delete(User $user, Model $model): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $model->school_id && $user->hasPermissionTo($this->permissionGroup().'.delete');
    }

    public function restore(User $user, Model $model): bool
    {
        return false;
    }

    public function forceDelete(User $user, Model $model): bool
    {
        return false;
    }
}
