<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

class UserPolicy extends TenantPolicy
{
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

        return (string) $user->school_id === (string) $model->school_id;
    }

    public function create(User $user): bool
    {
        if ($this->isSuperAdmin($user)) {
            return true;
        }

        return $this->isSchoolAdmin($user);
    }

    public function update(User $user, $model): bool
    {
        if ($this->isSuperAdmin($user)) {
            return true;
        }

        if (! $this->isSchoolAdmin($user)) {
            return false;
        }

        return (string) $user->school_id === (string) $model->school_id;
    }

    public function delete(User $user, $model): bool
    {
        if ($this->isSuperAdmin($user)) {
            return true;
        }

        if (! $this->isSchoolAdmin($user)) {
            return false;
        }

        if ((string) $user->school_id !== (string) $model->school_id) {
            return false;
        }

        return true;
    }

    public function forceDelete(User $user, $model): bool
    {
        return $this->isSuperAdmin($user);
    }
}
