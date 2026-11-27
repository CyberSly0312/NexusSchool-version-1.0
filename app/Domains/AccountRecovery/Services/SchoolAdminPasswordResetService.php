<?php

declare(strict_types=1);

namespace App\Domains\AccountRecovery\Services;

use App\Domains\AccountRecovery\DTOs\CompletedPasswordResetResult;
use App\Domains\AccountRecovery\DTOs\CompletePasswordResetCommand;
use App\Domains\AccountRecovery\DTOs\SchoolAdminResetCommand;
use App\Domains\AccountRecovery\DTOs\SchoolAdminResetResult;
use App\Domains\AccountRecovery\Enums\PasswordResetFailure;
use App\Domains\AccountRecovery\Exceptions\PasswordResetException;
use App\Domains\School\Models\School;
use App\Domains\School\Services\SchoolAdminProvisioningService;
use App\Domains\System\Enums\AuditAction;
use App\Domains\System\Services\AuditService;
use App\Domains\User\DTOs\AccessRevocationCommand;
use App\Domains\User\Policies\PasswordPolicy;
use App\Domains\User\Services\UserAccessRevocationService;
use App\Models\User;
use App\Models\UserInvitation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Throwable;

/**
 * Super Admin initiated School Admin password recovery.
 *
 * The Super Admin never supplies or receives a password. A short-lived,
 * single-use, hashed invitation is issued; only the School Admin defines the
 * new password on the school's own domain.
 */
final class SchoolAdminPasswordResetService
{
    public function __construct(
        private readonly UserAccessRevocationService $revocationService,
        private readonly AuditService $auditService,
    ) {
    }

    public function initiate(SchoolAdminResetCommand $command): SchoolAdminResetResult
    {
        /** @var User|null $actor */
        $actor = User::query()->find($command->actor->getKey());
        /** @var School|null $school */
        $school = School::query()->find($command->school->getKey());

        if (! $actor instanceof User || $actor->trashed() || ! $actor->isActive() || ! $actor->hasRole('super_admin')) {
            $this->reject($command->school, null, $command->actor, PasswordResetFailure::Actor, 'school_admin.password_reset.actor_rejected');
        }

        if (! $school instanceof School || $school->trashed() || ! $school->isActive()) {
            $this->reject($command->school, null, $actor, PasswordResetFailure::School, 'school_admin.password_reset.school_rejected');
        }

        /** @var User|null $target */
        $target = User::query()
            ->whereKey($command->targetUserId)
            ->where('school_id', $school->getKey())
            ->first();

        if (! $target instanceof User
            || $target->trashed()
            || ! $target->isActive()
            || ! $target->hasRole(SchoolAdminProvisioningService::ROLE)) {
            $this->reject($school, $target, $actor, PasswordResetFailure::Target, 'school_admin.password_reset.target_rejected');
        }

        try {
            return DB::transaction(function () use ($actor, $school, $target): SchoolAdminResetResult {
                $expiresAt = now()->addHours(UserInvitation::RESET_EXPIRATION_HOURS);
                $issue = UserInvitation::issue(
                    user: $target,
                    purpose: UserInvitation::PURPOSE_SCHOOL_ADMIN_RESET,
                    invitedBy: $actor,
                    expiresAt: $expiresAt,
                );

                $this->auditService->record([
                    'school_id' => $school->getKey(),
                    'auditable_type' => User::class,
                    'auditable_id' => (string) $target->getKey(),
                    'action' => AuditAction::PasswordResetRequested->value,
                    'event' => 'school_admin.password_reset.requested',
                    'description' => 'Lien de réinitialisation à usage unique généré pour l’administrateur d’établissement.',
                    'new_values' => [
                        'target_email' => $target->email,
                        'role' => SchoolAdminProvisioningService::ROLE,
                        'invitation_id' => (string) $issue->invitation->getKey(),
                        'expires_at' => $expiresAt->toIso8601String(),
                    ],
                    'user_id' => (string) $actor->getKey(),
                    'user_type' => User::class,
                ]);

                return new SchoolAdminResetResult(
                    $target->refresh(),
                    $issue->invitation,
                    $issue->plainToken,
                    $this->buildResetUrl($school, $issue->plainToken),
                    $expiresAt,
                );
            });
        } catch (PasswordResetException $exception) {
            throw $exception;
        } catch (Throwable) {
            $this->reject($school, $target, $actor, PasswordResetFailure::Persistence, 'school_admin.password_reset.failed');
        }
    }

    public function findResetForSchool(string $token, School $school): ?UserInvitation
    {
        $invitation = UserInvitation::findByPlainToken(trim($token));

        if (! $invitation instanceof UserInvitation
            || $invitation->purpose !== UserInvitation::PURPOSE_SCHOOL_ADMIN_RESET
            || (string) $invitation->school_id !== (string) $school->getKey()
            || ! $invitation->isUsable()) {
            return null;
        }

        /** @var User|null $target */
        $target = User::query()
            ->whereKey($invitation->user_id)
            ->where('school_id', $school->getKey())
            ->first();

        if (! $target instanceof User
            || $target->trashed()
            || ! $target->isActive()
            || ! $target->hasRole(SchoolAdminProvisioningService::ROLE)) {
            return null;
        }

        return $invitation;
    }

    public function complete(CompletePasswordResetCommand $command): CompletedPasswordResetResult
    {
        /** @var School|null $school */
        $school = School::query()->find($command->school->getKey());

        if (! $school instanceof School || $school->trashed() || ! $school->isActive()) {
            $this->reject($command->school, null, null, PasswordResetFailure::School, 'school_admin.password_reset.school_rejected');
        }

        $token = trim($command->token);
        $invitation = $token === '' ? null : UserInvitation::findByPlainToken($token);

        if (! $invitation instanceof UserInvitation) {
            $this->reject($school, null, null, PasswordResetFailure::Token, 'school_admin.password_reset.token_rejected');
        }

        /** @var User|null $target */
        $target = User::query()
            ->whereKey($invitation->user_id)
            ->where('school_id', $school->getKey())
            ->first();

        if ($invitation->purpose !== UserInvitation::PURPOSE_SCHOOL_ADMIN_RESET) {
            $this->reject($school, $target, null, PasswordResetFailure::Token, 'school_admin.password_reset.token_rejected');
        }

        if ($invitation->isConsumed()) {
            $this->reject($school, $target, null, PasswordResetFailure::TokenAlreadyUsed, 'school_admin.password_reset.token_rejected');
        }

        if ($invitation->isRevoked()) {
            $this->reject($school, $target, null, PasswordResetFailure::TokenRevoked, 'school_admin.password_reset.token_rejected');
        }

        if ($invitation->isExpired()) {
            $this->reject($school, $target, null, PasswordResetFailure::TokenExpired, 'school_admin.password_reset.token_rejected');
        }

        if ((string) $invitation->school_id !== (string) $school->getKey()) {
            $this->reject($school, $target, null, PasswordResetFailure::Token, 'school_admin.password_reset.token_rejected');
        }

        if (! $target instanceof User
            || $target->trashed()
            || ! $target->isActive()
            || ! $target->hasRole(SchoolAdminProvisioningService::ROLE)) {
            $this->reject($school, $target, null, PasswordResetFailure::Target, 'school_admin.password_reset.target_rejected');
        }

        $validator = Validator::make([
            'password' => $command->password,
            'password_confirmation' => $command->passwordConfirmation,
        ], [
            'password' => PasswordPolicy::privilegedRules(),
        ], PasswordPolicy::privilegedValidationMessages('password'));

        if ($validator->fails()) {
            /** @var list<string> $messages */
            $messages = $validator->errors()->all();
            $this->reject($school, $target, null, PasswordResetFailure::Validation, 'school_admin.password_reset.validation_rejected', $messages);
        }

        try {
            return DB::transaction(function () use ($school, $invitation, $target, $token, $command): CompletedPasswordResetResult {
                $freshInvitation = UserInvitation::findByPlainToken($token);

                if (! $freshInvitation instanceof UserInvitation
                    || ! $freshInvitation->isUsable()
                    || (string) $freshInvitation->getKey() !== (string) $invitation->getKey()) {
                    throw new PasswordResetException(PasswordResetFailure::TokenAlreadyUsed);
                }

                /** @var User $freshTarget */
                $freshTarget = User::query()->whereKey($target->getKey())->lockForUpdate()->firstOrFail();

                if (! $freshInvitation->consume()) {
                    throw new PasswordResetException(PasswordResetFailure::TokenAlreadyUsed);
                }

                $freshTarget->password = Hash::make($command->password);
                $freshTarget->save();

                $revocation = $this->revocationService->revoke(
                    AccessRevocationCommand::forAdministrativeReset($freshTarget, (string) $school->getKey()),
                );

                $this->auditService->record([
                    'school_id' => $school->getKey(),
                    'auditable_type' => User::class,
                    'auditable_id' => (string) $freshTarget->getKey(),
                    'action' => AuditAction::PasswordResetCompleted->value,
                    'event' => 'school_admin.password_reset.completed',
                    'description' => 'Mot de passe de l’administrateur d’établissement défini via le lien de réinitialisation.',
                    'new_values' => [
                        'invitation_id' => (string) $freshInvitation->getKey(),
                        'revocation' => $revocation->toAuditValues(),
                    ],
                    'user_id' => (string) $freshTarget->getKey(),
                    'user_type' => User::class,
                ]);

                return new CompletedPasswordResetResult($freshTarget->refresh(), $freshInvitation, $revocation);
            });
        } catch (PasswordResetException $exception) {
            $this->auditResetRejection($school, $target, $exception->failure, 'school_admin.password_reset.token_rejected');
            throw $exception;
        } catch (Throwable) {
            $this->auditResetRejection($school, $target, PasswordResetFailure::Persistence, 'school_admin.password_reset.failed');
            throw new PasswordResetException(PasswordResetFailure::Persistence);
        }
    }

    private function buildResetUrl(School $school, string $plainToken): string
    {
        $domain = trim((string) ($school->domains()->value('domain') ?? $school->domain));
        $path = route('school.account.password-reset.show', ['token' => $plainToken], false);
        $scheme = request()->getScheme() ?: 'https';

        return $scheme.'://'.$domain.$path;
    }

    /**
     * @param  list<string>  $validationMessages
     */
    private function reject(
        School $school,
        ?User $target,
        ?User $actor,
        PasswordResetFailure $failure,
        string $event,
        array $validationMessages = [],
    ): never {
        $this->auditResetRejection($school, $target, $failure, $event, $actor, $validationMessages);

        throw new PasswordResetException($failure, null, $validationMessages);
    }

    /**
     * @param  list<string>  $validationMessages
     */
    private function auditResetRejection(
        School $school,
        ?User $target,
        PasswordResetFailure $failure,
        string $event,
        ?User $actor = null,
        array $validationMessages = [],
    ): void {
        $values = ['reason' => $failure->value];
        if ($validationMessages !== []) {
            $values['validation_messages'] = $validationMessages;
        }

        $this->auditService->record([
            'school_id' => $school->getKey(),
            'auditable_type' => User::class,
            'auditable_id' => $target instanceof User
                ? (string) $target->getKey()
                : ($actor instanceof User ? (string) $actor->getKey() : (string) $school->getKey()),
            'action' => AuditAction::PasswordResetRejected->value,
            'event' => $event,
            'description' => 'Réinitialisation du mot de passe refusée.',
            'new_values' => $values,
            'user_id' => $actor instanceof User ? (string) $actor->getKey() : null,
            'user_type' => $actor instanceof User ? User::class : null,
        ]);
    }
}
