<?php

declare(strict_types=1);

namespace App\Domains\Library\Policies;

use App\Models\BookLoan;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BookLoanPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('library.view');
    }

    public function view(User $user, BookLoan $loan): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $loan->school_id && $user->hasPermissionTo('library.view');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('library.create');
    }

    public function update(User $user, BookLoan $loan): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $loan->school_id && $user->hasPermissionTo('library.update');
    }

    public function delete(User $user, BookLoan $loan): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $loan->school_id && $user->hasPermissionTo('library.delete');
    }

    public function restore(User $user, BookLoan $loan): bool
    {
        return false;
    }

    public function forceDelete(User $user, BookLoan $loan): bool
    {
        return false;
    }
}
