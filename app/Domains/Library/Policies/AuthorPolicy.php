<?php

declare(strict_types=1);

namespace App\Domains\Library\Policies;

use App\Models\Author;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AuthorPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('library.view');
    }

    public function view(User $user, Author $author): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $author->school_id && $user->hasPermissionTo('library.view');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('library.create');
    }

    public function update(User $user, Author $author): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $author->school_id && $user->hasPermissionTo('library.update');
    }

    public function delete(User $user, Author $author): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $author->school_id && $user->hasPermissionTo('library.delete');
    }

    public function restore(User $user, Author $author): bool
    {
        return false;
    }

    public function forceDelete(User $user, Author $author): bool
    {
        return false;
    }
}
