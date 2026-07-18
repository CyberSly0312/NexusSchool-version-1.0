<?php

declare(strict_types=1);

namespace App\Domains\Ai\Policies;

use App\Models\AiMessage;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AiMessagePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('ai.view');
    }

    public function view(User $user, AiMessage $model): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $model->school_id && $user->hasPermissionTo('ai.view');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('ai.create');
    }

    public function delete(User $user, AiMessage $model): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $model->school_id && $user->hasPermissionTo('ai.delete');
    }
}
