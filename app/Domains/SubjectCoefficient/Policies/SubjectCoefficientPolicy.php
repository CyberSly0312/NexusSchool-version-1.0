<?php

declare(strict_types=1);

namespace App\Domains\SubjectCoefficient\Policies;

use App\Models\SubjectCoefficient;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SubjectCoefficientPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('subject_coefficient.view');
    }

    public function view(User $user, SubjectCoefficient $model): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $model->school_id && $user->hasPermissionTo('subject_coefficient.view');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('subject_coefficient.create');
    }

    public function update(User $user, SubjectCoefficient $model): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $model->school_id && $user->hasPermissionTo('subject_coefficient.update');
    }

    public function delete(User $user, SubjectCoefficient $model): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $model->school_id && $user->hasPermissionTo('subject_coefficient.delete');
    }

    public function restore(User $user, SubjectCoefficient $model): bool
    {
        return false;
    }

    public function forceDelete(User $user, SubjectCoefficient $model): bool
    {
        return false;
    }
}
