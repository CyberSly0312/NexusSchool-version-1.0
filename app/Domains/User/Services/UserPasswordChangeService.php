<?php

declare(strict_types=1);

namespace App\Domains\User\Services;

use App\Domains\System\Enums\AuditAction;
use App\Domains\System\Services\AuditService;
use App\Domains\User\DTOs\AccessRevocationCommand;
use App\Domains\User\DTOs\PasswordChangeCommand;
use App\Domains\User\DTOs\PasswordChangeResult;
use App\Domains\User\Enums\PasswordChangeFailure;
use App\Domains\User\Exceptions\PasswordChangeException;
use App\Domains\User\Policies\PasswordPolicy;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Throwable;

/**
 * Personal password change. The actor is the target by construction; there is
 * no target identifier in the command, so another account cannot be selected.
 */
final class UserPasswordChangeService
{
    public function __construct(
        private readonly UserAccessRevocationService $revocationService,
        private readonly AuditService $auditService,
    ) {
    }

    public function change(PasswordChangeCommand $command): PasswordChangeResult
    {
        $actor = $this->resolveActor($command->actor);

        $validator = Validator::make([
            'current_password' => $command->currentPassword,
            'new_password' => $command->newPassword,
            'new_password_confirmation' => $command->newPasswordConfirmation,
        ], [
            'current_password' => ['required', 'string'],
            'new_password' => PasswordPolicy::personalRules(),
        ], array_merge(
            [
                'current_password.required' => 'Le mot de passe actuel est obligatoire.',
                'current_password.string' => 'Le mot de passe actuel doit être une chaîne de caractères.',
            ],
            PasswordPolicy::validationMessages('new_password'),
        ));

        if ($validator->fails()) {
            /** @var list<string> $messages */
            $messages = $validator->errors()->all();

            throw new PasswordChangeException(PasswordChangeFailure::Validation, null, $messages);
        }

        if (! Hash::check($command->currentPassword, $actor->password)) {
            $this->auditRejection($actor, PasswordChangeFailure::CurrentPassword);

            throw new PasswordChangeException(PasswordChangeFailure::CurrentPassword);
        }

        if (Hash::check($command->newPassword, $actor->password)) {
            $this->auditRejection($actor, PasswordChangeFailure::Validation);

            throw new PasswordChangeException(
                PasswordChangeFailure::Validation,
                'Le nouveau mot de passe doit être différent du mot de passe actuel.',
            );
        }

        try {
            return DB::transaction(function () use ($actor, $command): PasswordChangeResult {
                /** @var User $fresh */
                $fresh = User::query()->whereKey($actor->getKey())->lockForUpdate()->firstOrFail();

                $fresh->password = Hash::make($command->newPassword);
                $fresh->save();

                $revocation = $this->revocationService->revoke(
                    AccessRevocationCommand::forPasswordChange($fresh, $command->currentSessionId),
                );

                $this->auditService->record([
                    'school_id' => $fresh->school_id,
                    'auditable_type' => User::class,
                    'auditable_id' => (string) $fresh->getKey(),
                    'action' => AuditAction::PasswordChanged->value,
                    'event' => 'user.password.changed',
                    'description' => 'Mot de passe personnel modifié par l’utilisateur concerné.',
                    'new_values' => [
                        'scope' => 'self',
                        'other_sessions_revoked' => $revocation->sessionsDeleted,
                        'remember_token_revoked' => $revocation->rememberTokenRevoked,
                    ],
                    'user_id' => (string) $fresh->getKey(),
                    'user_type' => User::class,
                ]);

                return new PasswordChangeResult($fresh->refresh(), $revocation);
            });
        } catch (PasswordChangeException $exception) {
            throw $exception;
        } catch (Throwable) {
            throw new PasswordChangeException(PasswordChangeFailure::Persistence);
        }
    }

    private function resolveActor(User $actor): User
    {
        /** @var User|null $fresh */
        $fresh = User::query()->find($actor->getKey());

        if (! $fresh instanceof User || $fresh->trashed() || ! $fresh->isActive()) {
            throw new PasswordChangeException(PasswordChangeFailure::Actor);
        }

        return $fresh;
    }

    private function auditRejection(User $actor, PasswordChangeFailure $failure): void
    {
        $this->auditService->record([
            'school_id' => $actor->school_id,
            'auditable_type' => User::class,
            'auditable_id' => (string) $actor->getKey(),
            'action' => AuditAction::SensitiveOperationRejected->value,
            'event' => 'user.password.change_rejected',
            'description' => 'Modification du mot de passe refusée.',
            'new_values' => ['reason' => $failure->value, 'scope' => 'self'],
            'user_id' => (string) $actor->getKey(),
            'user_type' => User::class,
        ]);
    }
}
