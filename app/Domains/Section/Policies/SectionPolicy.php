<?php

declare(strict_types=1);

namespace App\Domains\Section\Policies;

use App\Models\Section;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SectionPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('section.view');
    }

    public function view(User $user, Section $section): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $section->school_id && $user->hasPermissionTo('section.view');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('section.create');
    }

    public function update(User $user, Section $section): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $section->school_id && $user->hasPermissionTo('section.update');
    }

    public function delete(User $user, Section $section): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $section->school_id && $user->hasPermissionTo('section.delete');
    }

    public function restore(User $user, Section $section): bool
    {
        return false;
    }

    public function forceDelete(User $user, Section $section): bool
    {
        return false;
    }
}
