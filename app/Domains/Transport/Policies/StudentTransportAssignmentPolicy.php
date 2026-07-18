<?php

declare(strict_types=1);

namespace App\Domains\Transport\Policies;

use App\Models\StudentTransportAssignment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StudentTransportAssignmentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('transport.view');
    }

    public function view(User $user, StudentTransportAssignment $model): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $model->school_id && $user->hasPermissionTo('transport.view');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('transport.create');
    }

    public function update(User $user, StudentTransportAssignment $model): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $model->school_id && $user->hasPermissionTo('transport.update');
    }

    public function delete(User $user, StudentTransportAssignment $model): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $model->school_id && $user->hasPermissionTo('transport.delete');
    }

    public function restore(User $user, StudentTransportAssignment $model): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $model->school_id && $user->hasPermissionTo('transport.delete');
    }

    public function forceDelete(User $user, StudentTransportAssignment $model): bool
    {
        return false;
    }
}
