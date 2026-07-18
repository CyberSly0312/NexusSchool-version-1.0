<?php

declare(strict_types=1);

namespace App\Domains\Analytics\Policies;

use App\Models\AnalyticsSnapshot;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AnalyticsSnapshotPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('analytics.view');
    }

    public function view(User $user, AnalyticsSnapshot $model): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $model->school_id && $user->hasPermissionTo('analytics.view');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('analytics.create');
    }

    public function update(User $user, AnalyticsSnapshot $model): bool
    {
        return false;
    }

    public function delete(User $user, AnalyticsSnapshot $model): bool
    {
        return false;
    }

    public function restore(User $user, AnalyticsSnapshot $model): bool
    {
        return false;
    }

    public function forceDelete(User $user, AnalyticsSnapshot $model): bool
    {
        return false;
    }
}
