<?php

declare(strict_types=1);

namespace App\Domains\Role;

use App\Domains\Role\Events\RoleAssignedToUser;
use App\Domains\Role\Events\RoleChangeRejected;
use App\Domains\Role\Events\RoleRevokedFromUser;
use App\Domains\Role\Listeners\AuditRoleChange;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

/**
 * Owning provider for role lifecycle listeners.
 *
 * Laravel 11 does not boot the legacy App\Providers\EventServiceProvider by
 * default, so these listeners are registered here as well as in the legacy map.
 */
final class RoleServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Event::listen(RoleAssignedToUser::class, [AuditRoleChange::class, 'handleRoleAssigned']);
        Event::listen(RoleRevokedFromUser::class, [AuditRoleChange::class, 'handleRoleRevoked']);
        Event::listen(RoleChangeRejected::class, [AuditRoleChange::class, 'handleRoleChangeRejected']);
    }
}
