<?php

declare(strict_types=1);

namespace App\Domains\RecordsLifecycle\Infrastructure\Policies;

use App\Domains\RecordsLifecycle\Infrastructure\Models\RecordLifecycle;
use App\Models\User;

final class RecordLifecyclePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('records_lifecycle.view');
    }

    public function view(User $user, RecordLifecycle $record): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $record->school_id && $user->hasPermissionTo('records_lifecycle.view');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('records_lifecycle.create');
    }

    public function update(User $user, RecordLifecycle $record): bool
    {
        return $user->hasRole('super_admin')
            || ($user->school_id === $record->school_id && $user->hasPermissionTo('records_lifecycle.update'));
    }

    public function delete(User $user, RecordLifecycle $record): bool
    {
        return false;
    }

    public function restore(User $user, RecordLifecycle $record): bool
    {
        return false;
    }

    public function forceDelete(User $user, RecordLifecycle $record): bool
    {
        return false;
    }

    public function publish(User $user, RecordLifecycle $record): bool
    {
        return $user->hasRole('super_admin')
            || ($user->school_id === $record->school_id && $user->hasPermissionTo('records_lifecycle.publish'));
    }

    public function archive(User $user, RecordLifecycle $record): bool
    {
        return $user->hasRole('super_admin')
            || ($user->school_id === $record->school_id && $user->hasPermissionTo('records_lifecycle.archive'));
    }
}
