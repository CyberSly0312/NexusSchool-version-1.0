<?php

declare(strict_types=1);

namespace App\Domains\Library\Policies;

use App\Models\Fine;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FinePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('library.view');
    }

    public function view(User $user, Fine $fine): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $fine->school_id && $user->hasPermissionTo('library.view');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('library.create');
    }

    public function update(User $user, Fine $fine): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $fine->school_id && $user->hasPermissionTo('library.update');
    }

    public function delete(User $user, Fine $fine): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $fine->school_id && $user->hasPermissionTo('library.delete');
    }

    public function restore(User $user, Fine $fine): bool
    {
        return false;
    }

    public function forceDelete(User $user, Fine $fine): bool
    {
        return false;
    }
}
