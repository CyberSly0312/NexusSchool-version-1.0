<?php

declare(strict_types=1);

namespace App\Domains\Timetable\Domain\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Authorization for the Timetable engine (SPR-007). Extends the SPR-002 school
 * resource pattern with the timetable-specific actions: publish, archive,
 * generate, duplicate, manage and resolve. All capabilities are school-scoped
 * and group-parameterized so the same trait backs the Timetable, ScheduleEntry
 * and TimetableConflict policies.
 */
trait HandlesTimetableAuthorization
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
        return $this->authorizeTimetableAction($user, $model, 'restore');
    }

    public function forceDelete(User $user, Model $model): bool
    {
        return false;
    }

    public function publish(User $user, ?Model $model = null): bool
    {
        return $this->authorizeTimetableAction($user, $model, 'publish');
    }

    public function archive(User $user, ?Model $model = null): bool
    {
        return $this->authorizeTimetableAction($user, $model, 'archive');
    }

    public function generate(User $user, ?Model $model = null): bool
    {
        return $this->authorizeTimetableAction($user, $model, 'generate');
    }

    public function duplicate(User $user, ?Model $model = null): bool
    {
        return $this->authorizeTimetableAction($user, $model, 'duplicate');
    }

    public function manage(User $user, ?Model $model = null): bool
    {
        return $this->authorizeTimetableAction($user, $model, 'manage');
    }

    public function resolve(User $user, ?Model $model = null): bool
    {
        return $this->authorizeTimetableAction($user, $model, 'resolve');
    }

    private function authorizeTimetableAction(User $user, ?Model $model, string $action): bool
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
