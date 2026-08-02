<?php

declare(strict_types=1);

namespace App\Domains\Student\Domain\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Authorization for student lifecycle operations. Extends the SPR-002 school
 * resource pattern with the domain-specific lifecycle permissions
 * (transfer / suspend / graduate / archive / restore).
 */
trait HandlesStudentAuthorization
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
        return $this->authorizeLifecycle($user, $model, 'restore');
    }

    public function forceDelete(User $user, Model $model): bool
    {
        return false;
    }

    public function transfer(User $user, Model $model): bool
    {
        return $this->authorizeLifecycle($user, $model, 'transfer');
    }

    public function suspend(User $user, Model $model): bool
    {
        return $this->authorizeLifecycle($user, $model, 'suspend');
    }

    public function graduate(User $user, Model $model): bool
    {
        return $this->authorizeLifecycle($user, $model, 'graduate');
    }

    public function archive(User $user, Model $model): bool
    {
        return $this->authorizeLifecycle($user, $model, 'archive');
    }

    private function authorizeLifecycle(User $user, Model $model, string $action): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $model->school_id && $user->hasPermissionTo($this->permissionGroup().'.'.$action);
    }
}
