<?php

declare(strict_types=1);

namespace App\Domains\Role\Listeners;

use App\Domains\Role\Events\RoleAssignedToUser;
use App\Domains\Role\Events\RoleChangeRejected;
use App\Domains\Role\Events\RoleRevokedFromUser;
use App\Domains\Role\Policies\SchoolAdminRolePolicy;
use App\Domains\System\Enums\AuditAction;
use App\Domains\System\Services\AuditService;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AuditRoleChange
{
    public function __construct(
        protected AuditService $audit,
    ) {
    }

    public function handleRoleAssigned(RoleAssignedToUser $event): void
    {
        $this->recordChange(
            target: $event->user,
            role: $event->role,
            actor: $event->actor,
            schoolId: $event->schoolId,
            action: AuditAction::RoleAssigned,
            operation: 'assign',
            outcome: $event->outcome,
            description: 'Rôle « '.SchoolAdminRolePolicy::label($event->role->name).' » attribué à l’utilisateur #'.$event->user->getKey().'.',
        );
    }

    public function handleRoleRevoked(RoleRevokedFromUser $event): void
    {
        $this->recordChange(
            target: $event->user,
            role: $event->role,
            actor: $event->actor,
            schoolId: $event->schoolId,
            action: AuditAction::RoleRemoved,
            operation: 'remove',
            outcome: $event->outcome,
            description: 'Rôle « '.SchoolAdminRolePolicy::label($event->role->name).' » retiré à l’utilisateur #'.$event->user->getKey().'.',
        );
    }

    public function handleRoleChangeRejected(RoleChangeRejected $event): void
    {
        $this->audit->record([
            'school_id' => $event->command->schoolId,
            'auditable_type' => User::class,
            'auditable_id' => (string) $event->command->targetUserId,
            'action' => AuditAction::RoleChangeRejected->value,
            'new_values' => [
                'target_user_id' => (string) $event->command->targetUserId,
                'role_ids' => array_map('strval', $event->command->roleIds),
                'school_id' => $event->command->schoolId,
                'operation' => $event->command->operation->value,
                'outcome' => 'rejected',
                'reason' => $event->reason,
            ],
            'event' => class_basename($event),
            'description' => 'Modification de rôle refusée pour l’utilisateur #'.$event->command->targetUserId.'.',
            'user_id' => (string) $event->actor->getKey(),
            'user_type' => $event->actor::class,
        ]);
    }

    /**
     * @param  'assign'|'remove'  $operation
     */
    private function recordChange(
        User $target,
        Role $role,
        ?User $actor,
        ?string $schoolId,
        AuditAction $action,
        string $operation,
        string $outcome,
        string $description,
    ): void {
        $this->audit->record([
            'school_id' => $schoolId ?? $target->school_id,
            'auditable_type' => User::class,
            'auditable_id' => (string) $target->getKey(),
            'action' => $action->value,
            'new_values' => [
                'actor_user_id' => $actor !== null ? (string) $actor->getKey() : null,
                'target_user_id' => (string) $target->getKey(),
                'role_id' => (string) $role->getKey(),
                'role_name' => $role->name,
                'school_id' => $schoolId ?? $target->school_id,
                'operation' => $operation,
                'outcome' => $outcome,
            ],
            'event' => $action === AuditAction::RoleAssigned ? class_basename(RoleAssignedToUser::class) : class_basename(RoleRevokedFromUser::class),
            'description' => $description,
            'user_id' => $actor !== null ? (string) $actor->getKey() : null,
            'user_type' => $actor !== null ? $actor::class : null,
        ]);
    }
}
