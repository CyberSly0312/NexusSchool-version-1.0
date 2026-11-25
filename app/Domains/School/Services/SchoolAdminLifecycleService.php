<?php

declare(strict_types=1);

namespace App\Domains\School\Services;

use App\Domains\School\DTOs\SchoolAdminLifecycleCommand;
use App\Domains\School\DTOs\SchoolAdminLifecycleResult;
use App\Domains\School\DTOs\SchoolAdminReplacementCommand;
use App\Domains\School\DTOs\SchoolAdminReplacementResult;
use App\Domains\School\Enums\SchoolAdminLifecycleFailure;
use App\Domains\School\Exceptions\SchoolAdminLifecycleException;
use App\Domains\School\Models\School;
use App\Domains\System\Enums\AuditAction;
use App\Domains\System\Services\AuditService;
use App\Domains\User\DTOs\AccessRevocationCommand;
use App\Domains\User\DTOs\AccountProvisioningCommand;
use App\Domains\User\DTOs\AccountProvisioningContext;
use App\Domains\User\Enums\UserStatus;
use App\Domains\User\Exceptions\AccountProvisioningException;
use App\Domains\User\Services\AccountProvisioningService;
use App\Domains\User\Services\UserAccessRevocationService;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Throwable;

/**
 * Departure handling for School Admins.
 *
 * The former account is never deleted: status changes to Suspended, all access
 * is centrally revoked, and history/ownership references stay intact. A
 * replacement is always a new account provisioned through the authorized
 * provisioning service with a different email address.
 */
final class SchoolAdminLifecycleService
{
    public function __construct(
        private readonly UserAccessRevocationService $revocationService,
        private readonly AccountProvisioningService $accountProvisioningService,
        private readonly SchoolAdminPermissionGrantService $permissionGrantService,
        private readonly AuditService $auditService,
    ) {
    }

    public function suspend(SchoolAdminLifecycleCommand $command): SchoolAdminLifecycleResult
    {
        $actor = $this->resolveActor($command);
        $school = $this->resolveSchool($command);
        $target = $this->resolveTarget($command, $school);

        if ($this->activeSchoolAdminCount($school) <= 1) {
            $this->reject($school, $target, $actor, SchoolAdminLifecycleFailure::LastActive, 'school_admin.suspension.last_active_rejected');
        }

        try {
            return DB::transaction(function () use ($actor, $school, $target): SchoolAdminLifecycleResult {
                $freshTarget = User::query()->whereKey($target->getKey())->lockForUpdate()->firstOrFail();

                $freshTarget->status = UserStatus::Suspended;
                $freshTarget->save();

                $revocation = $this->revocationService->revoke(
                    AccessRevocationCommand::forSuspension($freshTarget, (string) $school->getKey()),
                );

                $this->auditService->record([
                    'school_id' => $school->getKey(),
                    'auditable_type' => User::class,
                    'auditable_id' => (string) $freshTarget->getKey(),
                    'action' => AuditAction::AccessSuspended->value,
                    'event' => 'school_admin.access.suspended',
                    'description' => 'Accès de l’administrateur d’établissement sortant suspendu sans suppression du compte.',
                    'old_values' => ['status' => UserStatus::Active->value],
                    'new_values' => [
                        'status' => UserStatus::Suspended->value,
                        'revocation' => $revocation->toAuditValues(),
                    ],
                    'user_id' => (string) $actor->getKey(),
                    'user_type' => User::class,
                ]);

                return new SchoolAdminLifecycleResult($freshTarget->refresh(), $revocation);
            });
        } catch (SchoolAdminLifecycleException $exception) {
            throw $exception;
        } catch (Throwable) {
            throw new SchoolAdminLifecycleException(SchoolAdminLifecycleFailure::Persistence);
        }
    }

    public function replace(SchoolAdminReplacementCommand $command): SchoolAdminReplacementResult
    {
        $actor = $this->resolveActor($command);
        $school = $this->resolveSchool($command);
        $target = $this->resolveTarget($command, $school);

        $email = Str::lower(trim($command->email));
        $firstName = trim($command->firstName);
        $lastName = trim($command->lastName);

        if ($firstName === '' || $lastName === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $this->reject($school, $target, $actor, SchoolAdminLifecycleFailure::Email, 'school_admin.replacement.email_rejected');
        }

        if ($email === Str::lower(trim((string) $target->email))) {
            $this->reject($school, $target, $actor, SchoolAdminLifecycleFailure::IdentityTransfer, 'school_admin.replacement.identity_transfer_rejected');
        }

        if (User::withTrashed()->where('email', $email)->exists()) {
            $this->reject($school, $target, $actor, SchoolAdminLifecycleFailure::Email, 'school_admin.replacement.email_rejected');
        }

        try {
            return DB::transaction(function () use ($actor, $school, $target, $command, $email, $firstName, $lastName): SchoolAdminReplacementResult {
                /** @var School $freshSchool */
                $freshSchool = School::query()->whereKey($school->getKey())->lockForUpdate()->firstOrFail();
                $freshTarget = User::query()->whereKey($target->getKey())->lockForUpdate()->firstOrFail();

                $role = Role::findOrCreate(SchoolAdminProvisioningService::ROLE, 'web');

                $provisioning = $this->accountProvisioningService->provision(
                    new AccountProvisioningCommand(
                        schoolId: (string) $freshSchool->getKey(),
                        firstName: $firstName,
                        lastName: $lastName,
                        email: $email,
                        roleName: SchoolAdminProvisioningService::ROLE,
                        phone: $this->nullablePhone($command->phone),
                    ),
                    AccountProvisioningContext::forPlatform($actor),
                );

                $this->permissionGrantService->grant($role);

                // A distinct replacement account with a fresh invitation is the
                // safe replacement strategy documented for the last-active case.
                // It is intentionally pending until the new admin sets a password.
                $freshTarget->status = UserStatus::Suspended;
                $freshTarget->save();

                $revocation = $this->revocationService->revoke(
                    AccessRevocationCommand::forSuspension($freshTarget, (string) $freshSchool->getKey()),
                );

                $this->auditService->record([
                    'school_id' => $freshSchool->getKey(),
                    'auditable_type' => User::class,
                    'auditable_id' => (string) $provisioning->user->getKey(),
                    'action' => AuditAction::SchoolAdminReplaced->value,
                    'event' => 'school_admin.replacement.completed',
                    'description' => 'Nouvel administrateur d’établissement créé sans transférer l’identité du compte sortant.',
                    'new_values' => [
                        'former_admin_id' => (string) $freshTarget->getKey(),
                        'replacement_admin_id' => (string) $provisioning->user->getKey(),
                        'role' => SchoolAdminProvisioningService::ROLE,
                    ],
                    'user_id' => (string) $actor->getKey(),
                    'user_type' => User::class,
                ]);

                $this->auditService->record([
                    'school_id' => $freshSchool->getKey(),
                    'auditable_type' => User::class,
                    'auditable_id' => (string) $freshTarget->getKey(),
                    'action' => AuditAction::AccessSuspended->value,
                    'event' => 'school_admin.access.suspended',
                    'description' => 'Accès de l’administrateur d’établissement remplacé suspendu sans suppression du compte.',
                    'new_values' => [
                        'status' => UserStatus::Suspended->value,
                        'replacement_admin_id' => (string) $provisioning->user->getKey(),
                        'revocation' => $revocation->toAuditValues(),
                    ],
                    'user_id' => (string) $actor->getKey(),
                    'user_type' => User::class,
                ]);

                return new SchoolAdminReplacementResult(
                    $freshTarget->refresh(),
                    $provisioning->user->refresh(),
                    $this->buildActivationUrl($freshSchool, $provisioning->invitationIssue->plainToken),
                    $revocation,
                );
            });
        } catch (SchoolAdminLifecycleException $exception) {
            $this->auditRejection($school, $target, $actor, $exception->failure, 'school_admin.replacement.rejected');

            throw $exception;
        } catch (AccountProvisioningException) {
            $failure = SchoolAdminLifecycleFailure::Provisioning;
            $this->auditRejection($school, $target, $actor, $failure, 'school_admin.replacement.rejected');

            throw new SchoolAdminLifecycleException($failure);
        } catch (Throwable) {
            throw new SchoolAdminLifecycleException(SchoolAdminLifecycleFailure::Persistence);
        }
    }

    private function resolveActor(SchoolAdminLifecycleCommand|SchoolAdminReplacementCommand $command): User
    {
        /** @var User|null $actor */
        $actor = User::query()->find($command->actor->getKey());

        if (! $actor instanceof User
            || $actor->trashed()
            || ! $actor->isActive()
            || ! $actor->hasRole('super_admin')) {
            $this->reject($command->school, null, $command->actor, SchoolAdminLifecycleFailure::Actor, 'school_admin.lifecycle.actor_rejected');
        }

        return $actor;
    }

    private function resolveSchool(SchoolAdminLifecycleCommand|SchoolAdminReplacementCommand $command): School
    {
        /** @var School|null $school */
        $school = School::query()->find($command->school->getKey());

        if (! $school instanceof School || $school->trashed() || ! $school->isActive()) {
            $this->reject($command->school, null, null, SchoolAdminLifecycleFailure::School, 'school_admin.lifecycle.school_rejected');
        }

        return $school;
    }

    private function resolveTarget(SchoolAdminLifecycleCommand|SchoolAdminReplacementCommand $command, School $school): User
    {
        /** @var User|null $target */
        $target = User::query()
            ->whereKey($command->targetUserId)
            ->where('school_id', $school->getKey())
            ->first();

        if (! $target instanceof User
            || $target->trashed()
            || ! $target->isActive()
            || ! $target->hasRole(SchoolAdminProvisioningService::ROLE)) {
            $this->reject($school, $target, null, SchoolAdminLifecycleFailure::Target, 'school_admin.lifecycle.target_rejected');
        }

        return $target;
    }

    private function activeSchoolAdminCount(School $school): int
    {
        return User::query()
            ->where('school_id', $school->getKey())
            ->where('status', UserStatus::Active)
            ->whereHas('roles', fn ($query) => $query->where('name', SchoolAdminProvisioningService::ROLE))
            ->count();
    }

    private function nullablePhone(?string $phone): ?string
    {
        $phone = $phone === null ? null : trim($phone);

        return $phone === null || $phone === '' ? null : $phone;
    }

    private function buildActivationUrl(School $school, string $plainToken): string
    {
        $domain = trim((string) ($school->domains()->value('domain') ?? $school->domain));
        $path = route('school.account.activation.show', ['token' => $plainToken], false);
        $scheme = request()->getScheme() ?: 'https';

        return $scheme.'://'.$domain.$path;
    }

    private function reject(
        School $school,
        ?User $target,
        ?User $actor,
        SchoolAdminLifecycleFailure $failure,
        string $event,
    ): never {
        $this->auditRejection($school, $target, $actor, $failure, $event);

        throw new SchoolAdminLifecycleException($failure);
    }

    private function auditRejection(
        School $school,
        ?User $target,
        ?User $actor,
        SchoolAdminLifecycleFailure $failure,
        string $event,
    ): void {
        $this->auditService->record([
            'school_id' => $school->getKey(),
            'auditable_type' => User::class,
            'auditable_id' => $target !== null
                ? (string) $target->getKey()
                : ($actor instanceof User ? (string) $actor->getKey() : (string) $school->getKey()),
            'action' => AuditAction::SensitiveOperationRejected->value,
            'event' => $event,
            'description' => 'Opération sensible sur le cycle de vie d’un administrateur refusée.',
            'new_values' => ['reason' => $failure->value],
            'user_id' => $actor instanceof User ? (string) $actor->getKey() : null,
            'user_type' => $actor instanceof User ? User::class : null,
        ]);
    }
}
