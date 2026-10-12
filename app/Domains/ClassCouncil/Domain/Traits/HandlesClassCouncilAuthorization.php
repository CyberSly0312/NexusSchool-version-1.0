<?php

declare(strict_types=1);

namespace App\Domains\ClassCouncil\Domain\Traits;

use App\Contracts\SchoolOwned;
use App\Models\User;

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

    public function view(User $user, SchoolOwned $model): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $model->getSchoolId() && $user->hasPermissionTo($this->permissionGroup().'.view');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo($this->permissionGroup().'.create');
    }

    public function update(User $user, SchoolOwned $model): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $model->getSchoolId() && $user->hasPermissionTo($this->permissionGroup().'.update');
    }

    public function delete(User $user, SchoolOwned $model): bool
    {
        return false;
    }

    public function restore(User $user, SchoolOwned $model): bool
    {
        return false;
    }

    public function forceDelete(User $user, SchoolOwned $model): bool
    {
        return false;
    }

    public function convene(User $user, SchoolOwned $model): bool
    {
        return $this->authorizeLifecycle($user, $model, 'convene');
    }

    public function decide(User $user, SchoolOwned $model): bool
    {
        return $this->authorizeLifecycle($user, $model, 'decide');
    }

    public function publish(User $user, SchoolOwned $model): bool
    {
        return $this->authorizeLifecycle($user, $model, 'publish');
    }

    public function archive(User $user, SchoolOwned $model): bool
    {
        return $this->authorizeLifecycle($user, $model, 'archive');
    }

    private function authorizeLifecycle(User $user, SchoolOwned $model, string $action): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $model->getSchoolId() && $user->hasPermissionTo($this->permissionGroup().'.'.$action);
    }
}
