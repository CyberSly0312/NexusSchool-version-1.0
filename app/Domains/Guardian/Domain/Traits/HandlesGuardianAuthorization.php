<?php

declare(strict_types=1);

namespace App\Domains\Guardian\Domain\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Authorization for guardian and student–guardian relationship operations.
 * Extends the SPR-002 school resource pattern with the domain-specific
 * relationship permissions (link / unlink / primary / emergency / pickup).
 */
trait HandlesGuardianAuthorization
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
        return $this->authorizeRelationship($user, $model, 'restore');
    }

    public function forceDelete(User $user, Model $model): bool
    {
        return false;
    }

    public function link(User $user, ?Model $model = null): bool
    {
        return $this->authorizeRelationship($user, $model, 'link');
    }

    public function unlink(User $user, Model $model): bool
    {
        return $this->authorizeRelationship($user, $model, 'unlink');
    }

    public function assignPrimary(User $user, Model $model): bool
    {
        return $this->authorizeRelationship($user, $model, 'primary');
    }

    public function assignEmergency(User $user, Model $model): bool
    {
        return $this->authorizeRelationship($user, $model, 'emergency');
    }

    public function assignPickup(User $user, Model $model): bool
    {
        return $this->authorizeRelationship($user, $model, 'pickup');
    }

    private function authorizeRelationship(User $user, ?Model $model, string $action): bool
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
