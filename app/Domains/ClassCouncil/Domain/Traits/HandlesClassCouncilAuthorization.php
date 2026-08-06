<?php

declare(strict_types=1);

namespace App\Domains\ClassCouncil\Domain\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Authorization for class council lifecycle operations (SPR-013). Extends the
 * SPR-002 school resource pattern with the council lifecycle permissions
 * (convene, decide, publish, archive).
 */
trait HandlesClassCouncilAuthorization
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
        return false;
    }

    public function restore(User $user, Model $model): bool
    {
        return false;
    }

    public function forceDelete(User $user, Model $model): bool
    {
        return false;
    }

    public function convene(User $user, Model $model): bool
    {
        return $this->authorizeLifecycle($user, $model, 'convene');
    }

    public function decide(User $user, Model $model): bool
    {
        return $this->authorizeLifecycle($user, $model, 'decide');
    }

    public function publish(User $user, Model $model): bool
    {
        return $this->authorizeLifecycle($user, $model, 'publish');
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
