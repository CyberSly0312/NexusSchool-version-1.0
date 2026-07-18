<?php

declare(strict_types=1);

namespace App\Domains\Communication\Policies;

use App\Models\MessageRecipient;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MessageRecipientPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('communication.view');
    }

    public function view(User $user, MessageRecipient $recipient): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return ($user->school_id === $recipient->school_id && $user->hasPermissionTo('communication.view'))
            || $recipient->recipient_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('communication.create');
    }

    public function delete(User $user, MessageRecipient $recipient): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $recipient->school_id && $user->hasPermissionTo('communication.delete');
    }
}
