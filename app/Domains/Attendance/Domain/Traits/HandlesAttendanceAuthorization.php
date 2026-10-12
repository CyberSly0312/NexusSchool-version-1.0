<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Domain\Traits;

use App\Contracts\SchoolOwned;
use App\Models\User;

/**
 * Authorization for the Attendance engine (SPR-008).
 *
 * Access is granted exclusively through the attendance capabilities —
 * view / record / correct / lock / unlock / report / manage — never through
 * role membership. The super_admin bypass is the platform-wide superuser
 * escape hatch shared by every domain policy. All capabilities are
 * school-scoped and group-parameterized so a single trait backs the Session,
 * Record, Correction, Alert and Summary policies.
 */
trait HandlesAttendanceAuthorization
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
        return $user->hasRole('super_admin') || $user->hasPermissionTo($this->permissionGroup().'.record');
    }

    public function update(User $user, SchoolOwned $model): bool
    {
        return $this->authorizeAttendanceAction($user, $model, 'record');
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

    public function record(User $user, ?SchoolOwned $model = null): bool
    {
        return $this->authorizeAttendanceAction($user, $model, 'record');
    }

    public function correct(User $user, ?SchoolOwned $model = null): bool
    {
        return $this->authorizeAttendanceAction($user, $model, 'correct');
    }

    public function lock(User $user, ?SchoolOwned $model = null): bool
    {
        return $this->authorizeAttendanceAction($user, $model, 'lock');
    }

    public function unlock(User $user, ?SchoolOwned $model = null): bool
    {
        return $this->authorizeAttendanceAction($user, $model, 'unlock');
    }

    public function report(User $user, ?SchoolOwned $model = null): bool
    {
        return $this->authorizeAttendanceAction($user, $model, 'report');
    }

    public function manage(User $user, ?SchoolOwned $model = null): bool
    {
        return $this->authorizeAttendanceAction($user, $model, 'manage');
    }

    private function authorizeAttendanceAction(User $user, ?SchoolOwned $model, string $action): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        if ($model !== null && $user->school_id !== $model->getSchoolId()) {
            return false;
        }

        return $user->hasPermissionTo($this->permissionGroup().'.'.$action);
    }
}
