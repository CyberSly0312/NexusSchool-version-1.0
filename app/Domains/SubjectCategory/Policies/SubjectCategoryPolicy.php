<?php

declare(strict_types=1);

namespace App\Domains\SubjectCategory\Policies;

use App\Models\SubjectCategory;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SubjectCategoryPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('subject_category.view');
    }

    public function view(User $user, SubjectCategory $model): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $model->school_id && $user->hasPermissionTo('subject_category.view');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('subject_category.create');
    }

    public function update(User $user, SubjectCategory $model): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $model->school_id && $user->hasPermissionTo('subject_category.update');
    }

    public function delete(User $user, SubjectCategory $model): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $model->school_id && $user->hasPermissionTo('subject_category.delete');
    }

    public function restore(User $user, SubjectCategory $model): bool
    {
        return false;
    }

    public function forceDelete(User $user, SubjectCategory $model): bool
    {
        return false;
    }
}
