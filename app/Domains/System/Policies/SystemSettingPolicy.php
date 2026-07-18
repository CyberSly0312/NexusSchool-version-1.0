<?php

declare(strict_types=1);

namespace App\Domains\System\Policies;

use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SystemSettingPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin');
    }

    public function view(User $user, SystemSetting $setting): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return isset($setting->school_id)
            ? $user->school_id === $setting->school_id
            : false;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin');
    }

    public function update(User $user, SystemSetting $setting): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return isset($setting->school_id)
            ? $user->school_id === $setting->school_id
            : false;
    }

    public function delete(User $user, SystemSetting $setting): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return isset($setting->school_id)
            ? $user->school_id === $setting->school_id
            : false;
    }
}
