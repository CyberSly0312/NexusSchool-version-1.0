<?php

declare(strict_types=1);

namespace App\Domains\System\Policies;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AuditLogPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('audit_log.view');
    }

    public function view(User $user, AuditLog $model): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $model->school_id && $user->hasPermissionTo('audit_log.view');
    }

    public function export(User $user): bool
    {
        return $user->hasRole('super_admin');
    }
}
