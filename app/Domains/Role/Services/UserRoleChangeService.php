<?php

declare(strict_types=1);

namespace App\Domains\Role\Services;

use App\Domains\Role\DTOs\RoleChangeCommand;
use App\Domains\Role\DTOs\RoleChangeContext;
use App\Domains\Role\DTOs\RoleChangePlan;
use App\Domains\Role\DTOs\RoleChangeResult;
use App\Domains\Role\Enums\RoleChangeOperation;
use App\Domains\Role\Events\RoleAssignedToUser;
use App\Domains\Role\Events\RoleChangeRejected;
use App\Domains\Role\Events\RoleRevokedFromUser;
use App\Domains\Role\Exceptions\RoleChangeException;
use App\Domains\Role\Policies\SchoolAdminRolePolicy;
use App\Domains\School\Models\School;
use App\Domains\User\Enums\UserStatus;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

/**
 * Single server-side entry point for assigning/removing roles on an existing user.
 *
 * The service resolves the target, school and roles from persisted identifiers.
 * No role name, school identifier or visibility option supplied by the browser is
 * trusted for authorization.
 */
class UserRoleChangeService
{
    public function validate(RoleChangeCommand $command, RoleChangeContext $context): RoleChangePlan
    {
        try {
            return $this->makePlan($command, $context);
        } catch (RoleChangeException $exception) {
            RoleChangeRejected::dispatch($context->actor, $command, $exception->reason);

            throw $exception;
        }
    }

    public function apply(RoleChangeCommand $command, RoleChangeContext $context): RoleChangeResult
    {
        $plan = $this->validate($command, $context);

        return DB::transaction(function () use ($plan, $command, $context): RoleChangeResult {
            $currentRoleIds = $plan->target->roles()->pluck('id')->all();
            $changed = false;

            foreach ($plan->roles as $role) {
                $roleId = (int) $role->getKey();
                $isAssigned = in_array($roleId, $currentRoleIds, true);

                if ($command->operation === RoleChangeOperation::Assign) {
                    if ($isAssigned) {
                        continue;
                    }

                    $plan->target->assignRole($role);
                    $currentRoleIds[] = $roleId;
                    $changed = true;

                    RoleAssignedToUser::dispatch(
                        $plan->target,
                        $role,
                        $context->actor,
                        $plan->schoolId,
                        'assigned',
                    );

                    continue;
                }

                if (! $isAssigned) {
                    continue;
                }

                $plan->target->removeRole($role);
                $currentRoleIds = array_values(array_diff($currentRoleIds, [$roleId]));
                $changed = true;

                RoleRevokedFromUser::dispatch(
                    $plan->target,
                    $role,
                    $context->actor,
                    $plan->schoolId,
                    'removed',
                );
            }

            return new RoleChangeResult(
                $plan->target->refresh(),
                $plan->roles,
                $plan->schoolId,
                $command->operation,
                $changed,
            );
        });
    }

    /**
     * Cheap target/school check used when a relation manager mounts. It performs
     * no audit write and mutates nothing.
     */
    public function assertTargetIsAccessible(User $target, RoleChangeContext $context): void
    {
        if ($target->trashed()) {
            throw new RoleChangeException(
                'L’utilisateur cible n’est pas disponible.',
                RoleChangeException::REASON_TARGET,
            );
        }

        if (! $context->isSchoolAdminOperation()) {
            return;
        }

        $tenantSchoolId = (string) ($context->tenantSchoolId ?? '');
        $targetSchoolId = (string) ($target->school_id ?? '');

        if ($tenantSchoolId === '' || $targetSchoolId === '' || $tenantSchoolId !== $targetSchoolId) {
            throw new RoleChangeException(
                'L’utilisateur cible n’appartient pas à l’établissement courant.',
                RoleChangeException::REASON_SCHOOL,
            );
        }
    }

    private function makePlan(RoleChangeCommand $command, RoleChangeContext $context): RoleChangePlan
    {
        if ($command->roleIds === []) {
            throw new RoleChangeException(
                'Aucun rôle valide n’a été fourni.',
                RoleChangeException::REASON_ROLE,
            );
        }

        foreach ($command->roleIds as $roleId) {
            if ($roleId <= 0) {
                throw new RoleChangeException(
                    'Le rôle demandé est invalide.',
                    RoleChangeException::REASON_ROLE,
                );
            }
        }

        $target = User::withTrashed()->find((string) $command->targetUserId);
        if (! $target instanceof User || $target->trashed()) {
            throw new RoleChangeException(
                'L’utilisateur cible n’est pas disponible.',
                RoleChangeException::REASON_TARGET,
            );
        }

        $schoolId = $this->resolveSchoolId($command, $context, $target);
        $school = $schoolId !== null ? School::query()->find($schoolId) : null;

        if ($context->isSchoolAdminOperation() && (! $school instanceof School || ! $school->isActive())) {
            throw new RoleChangeException(
                'L’établissement courant n’est pas disponible.',
                RoleChangeException::REASON_SCHOOL,
            );
        }

        $roles = Role::query()
            ->whereIn('id', $command->roleIds)
            ->where('guard_name', 'web')
            ->get()
            ->values()
            ->all();

        if (count($roles) !== count($command->roleIds)) {
            throw new RoleChangeException(
                'Le rôle demandé est invalide.',
                RoleChangeException::REASON_ROLE,
            );
        }

        if ($context->isSchoolAdminOperation()) {
            foreach ($roles as $role) {
                if (! SchoolAdminRolePolicy::allows($role->name)) {
                    throw new RoleChangeException(
                        'Ce rôle ne peut pas être modifié par un administrateur d’établissement.',
                        RoleChangeException::REASON_ROLE,
                    );
                }
            }
        }

        $this->assertInvariants($target, $roles, $command, $context);

        return new RoleChangePlan($target, $roles, $school, $schoolId, $context);
    }

    private function resolveSchoolId(
        RoleChangeCommand $command,
        RoleChangeContext $context,
        User $target,
    ): ?string {
        $submittedSchoolId = $command->schoolId !== null && trim($command->schoolId) !== ''
            ? trim($command->schoolId)
            : null;
        $targetSchoolId = (string) ($target->school_id ?? '');

        if ($context->isSchoolAdminOperation()) {
            $expectedSchoolId = (string) ($context->tenantSchoolId ?? '');

            if (
                $expectedSchoolId === ''
                || $targetSchoolId === ''
                || $targetSchoolId !== $expectedSchoolId
                || ($submittedSchoolId !== null && $submittedSchoolId !== $expectedSchoolId)
            ) {
                throw new RoleChangeException(
                    'L’utilisateur cible n’appartient pas à l’établissement courant.',
                    RoleChangeException::REASON_SCHOOL,
                );
            }

            return $expectedSchoolId;
        }

        if ($submittedSchoolId !== null && $targetSchoolId !== '' && $submittedSchoolId !== $targetSchoolId) {
            throw new RoleChangeException(
                'L’établissement de la cible est incohérent.',
                RoleChangeException::REASON_SCHOOL,
            );
        }

        return $targetSchoolId !== '' ? $targetSchoolId : null;
    }

    /**
     * @param  list<Role>  $roles
     */
    private function assertInvariants(
        User $target,
        array $roles,
        RoleChangeCommand $command,
        RoleChangeContext $context,
    ): void {
        if (
            $command->operation === RoleChangeOperation::Remove
            && (string) $target->getKey() === (string) $context->actor->getKey()
        ) {
            throw new RoleChangeException(
                'Un administrateur ne peut pas retirer son propre accès.',
                RoleChangeException::REASON_INVARIANT,
            );
        }

        if ($command->operation !== RoleChangeOperation::Remove) {
            return;
        }

        $removesSchoolAdmin = false;
        foreach ($roles as $role) {
            if ($role->name === 'school_admin') {
                $removesSchoolAdmin = true;
                break;
            }
        }

        if (! $removesSchoolAdmin) {
            return;
        }

        $schoolId = (string) ($target->school_id ?? '');

        if ($schoolId === '' || ! $target->isActive()) {
            return;
        }

        $activeSchoolAdminCount = User::query()
            ->where('school_id', $schoolId)
            ->where('status', UserStatus::Active->value)
            ->whereHas('roles', function ($query): void {
                $query
                    ->where('name', 'school_admin')
                    ->where('guard_name', 'web');
            })
            ->count();

        if ($activeSchoolAdminCount <= 1) {
            throw new RoleChangeException(
                'Le dernier administrateur actif de l’établissement doit être conservé.',
                RoleChangeException::REASON_INVARIANT,
            );
        }
    }
}
