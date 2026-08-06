<?php

declare(strict_types=1);

namespace App\Domains\Staff\Domain\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Authorization for the Staff & Workforce Engine. Extends the SPR-002 school
 * resource pattern with the workforce-specific actions (assign, transfer,
 * terminate, suspend, restore, teach).
 */
trait HandlesStaffAuthorization
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
        return $this->authorizeWorkforceAction($user, $model, 'restore');
    }

    public function forceDelete(User $user, Model $model): bool
    {
        return false;
    }

    public function assign(User $user, ?Model $model = null): bool
    {
        return $this->authorizeWorkforceAction($user, $model, 'assign');
    }

    public function transfer(User $user, ?Model $model = null): bool
    {
        return $this->authorizeWorkforceAction($user, $model, 'transfer');
    }

    public function terminate(User $user, ?Model $model = null): bool
    {
        return $this->authorizeWorkforceAction($user, $model, 'terminate');
    }

    public function suspend(User $user, ?Model $model = null): bool
    {
        return $this->authorizeWorkforceAction($user, $model, 'suspend');
    }

    public function teach(User $user, ?Model $model = null): bool
    {
        return $this->authorizeWorkforceAction($user, $model, 'teach');
    }

    private function authorizeWorkforceAction(User $user, ?Model $model, string $action): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        if ($model !== null && $user->school_id !== $model->school_id) {
            return false;
        }

        return $user->hasPermissionTo($this->permissionGroup().'.'.$action);
    }
}
