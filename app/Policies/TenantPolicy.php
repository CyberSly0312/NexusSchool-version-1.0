<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

abstract class TenantPolicy
{
    use HandlesAuthorization;

    protected function belongsToCurrentTenant($model): bool
    {
        if (! $model || ! method_exists($model, 'school_id')) {
            return false;
        }

        $tenantId = tenant()?->id;

        if (! $tenantId) {
            return false;
        }

        return (string) $model->school_id === (string) $tenantId;
    }

    protected function isSuperAdmin(User $user): bool
    {
        return $user->hasRole('super_admin');
    }

    protected function isSchoolAdmin(User $user): bool
    {
        return $user->hasRole('school_admin');
    }

    protected function belongsToSameSchool(User $user, $model): bool
    {
        if (! $model || ! method_exists($model, 'school_id')) {
            return false;
        }

        return (string) $user->school_id === (string) $model->school_id;
    }

    public function viewAny(User $user): bool
    {
        if ($this->isSuperAdmin($user)) {
            return true;
        }

        return $user->school_id !== null;
    }

    public function view(User $user, $model): bool
    {
        if ($this->isSuperAdmin($user)) {
            return true;
        }

        return $this->belongsToSameSchool($user, $model);
    }

    public function create(User $user): bool
    {
        if ($this->isSuperAdmin($user)) {
            return true;
        }

        return $user->school_id !== null;
    }

    public function update(User $user, $model): bool
    {
        if ($this->isSuperAdmin($user)) {
            return true;
        }

        return $this->belongsToSameSchool($user, $model);
    }

    public function delete(User $user, $model): bool
    {
        if ($this->isSuperAdmin($user)) {
            return true;
        }

        return $this->belongsToSameSchool($user, $model);
    }

    public function restore(User $user, $model): bool
    {
        if ($this->isSuperAdmin($user)) {
            return true;
        }

        return $this->belongsToSameSchool($user, $model);
    }

    public function forceDelete(User $user, $model): bool
    {
        return $this->isSuperAdmin($user);
    }
}
