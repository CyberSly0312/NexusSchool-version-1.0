<?php

declare(strict_types=1);

namespace App\Domains\ReportCard\Domain\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Authorization for report card lifecycle operations. Extends the SPR-002
 * school resource pattern with the report card lifecycle permissions
 * (generate, regenerate, publish).
 */
trait HandlesReportCardAuthorization
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

    public function generate(User $user, Model $model): bool
    {
        return $this->authorizeLifecycle($user, $model, 'generate');
    }

    public function regenerate(User $user, Model $model): bool
    {
        return $this->authorizeLifecycle($user, $model, 'regenerate');
    }

    public function publish(User $user, Model $model): bool
    {
        return $this->authorizeLifecycle($user, $model, 'publish');
    }

    private function authorizeLifecycle(User $user, Model $model, string $action): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $model->school_id && $user->hasPermissionTo($this->permissionGroup().'.'.$action);
    }
}
