<?php

declare(strict_types=1);

namespace App\Domains\AcademicPolicy\Domain\Traits;

use App\Contracts\SchoolOwned;
use App\Models\User;

/**
 * Authorization for the Academic Policies engine (SPR-009.5).
 *
 * Follows the SPR-002 school resource pattern: super_admin bypasses; otherwise
 * the user must belong to the policy's school and hold the
 * `academic_policy.{action}` permission. Ownership is expressed through the
 * {@see SchoolOwned} contract rather than Eloquent magic properties.
 */
trait HandlesAcademicPolicyAuthorization
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
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $model->getSchoolId() && $user->hasPermissionTo($this->permissionGroup().'.delete');
    }

    public function restore(User $user, SchoolOwned $model): bool
    {
        return false;
    }

    public function forceDelete(User $user, SchoolOwned $model): bool
    {
        return false;
    }
}
