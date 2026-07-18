<?php

declare(strict_types=1);

namespace App\Domains\Library\Policies;

use App\Models\Reservation;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReservationPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('library.view');
    }

    public function view(User $user, Reservation $reservation): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $reservation->school_id && $user->hasPermissionTo('library.view');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('library.create');
    }

    public function update(User $user, Reservation $reservation): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $reservation->school_id && $user->hasPermissionTo('library.update');
    }

    public function delete(User $user, Reservation $reservation): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $reservation->school_id && $user->hasPermissionTo('library.delete');
    }

    public function restore(User $user, Reservation $reservation): bool
    {
        return false;
    }

    public function forceDelete(User $user, Reservation $reservation): bool
    {
        return false;
    }
}
