<?php

declare(strict_types=1);

namespace App\Domains\Workflow\Policies;

use App\Models\User;
use App\Models\WorkflowExecution;
use Illuminate\Auth\Access\HandlesAuthorization;

class WorkflowExecutionPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('workflow.view');
    }

    public function view(User $user, WorkflowExecution $model): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $model->school_id && $user->hasPermissionTo('workflow.view');
    }

    public function cancel(User $user, WorkflowExecution $model): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $model->school_id && $user->hasPermissionTo('workflow.manage');
    }

    public function retry(User $user, WorkflowExecution $model): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $model->school_id && $user->hasPermissionTo('workflow.execute');
    }
}
