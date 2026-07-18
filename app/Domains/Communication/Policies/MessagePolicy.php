<?php

declare(strict_types=1);

namespace App\Domains\Communication\Policies;

use App\Models\Message;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MessagePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('communication.view');
    }

    public function view(User $user, Message $message): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return ($user->school_id === $message->school_id && $user->hasPermissionTo('communication.view'))
            || $message->sender_id === $user->id
            || $message->recipients()->where('recipient_id', $user->id)->exists();
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('communication.create');
    }

    public function update(User $user, Message $message): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return ($user->school_id === $message->school_id && $user->hasPermissionTo('communication.update'))
            || $message->sender_id === $user->id;
    }

    public function delete(User $user, Message $message): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return ($user->school_id === $message->school_id && $user->hasPermissionTo('communication.delete'))
            || $message->sender_id === $user->id;
    }

    public function restore(User $user, Message $message): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $message->school_id && $user->hasPermissionTo('communication.delete');
    }

    public function forceDelete(User $user, Message $message): bool
    {
        return false;
    }
}
