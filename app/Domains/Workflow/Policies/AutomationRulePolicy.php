<?php

declare(strict_types=1);

namespace App\Domains\Workflow\Policies;

use App\Models\AutomationRule;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AutomationRulePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('workflow.view');
    }

    public function view(User $user, AutomationRule $model): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $model->school_id && $user->hasPermissionTo('workflow.view');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('workflow.create');
    }

    public function update(User $user, AutomationRule $model): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $model->school_id && $user->hasPermissionTo('workflow.update');
    }

    public function delete(User $user, AutomationRule $model): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $model->school_id && $user->hasPermissionTo('workflow.delete');
    }
}
