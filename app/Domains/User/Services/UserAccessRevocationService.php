<?php

declare(strict_types=1);

namespace App\Domains\User\Services;

use App\Domains\Api\Services\ApiTokenService;
use App\Domains\School\Models\SchoolAdminSetupToken;
use App\Domains\User\DTOs\AccessRevocationCommand;
use App\Domains\User\DTOs\AccessRevocationResult;
use App\Domains\User\Enums\AccessRevocationFailure;
use App\Domains\User\Exceptions\AccessRevocationException;
use App\Models\User;
use App\Models\UserInvitation;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;
use Throwable;

/**
 * The single entry point for invalidating every persistent access mechanism
 * discovered in Task 10A: database sessions, remember tokens, Laravel password
 * reset tokens, API tokens, invitations/setup tokens and the Spatie cache.
 *
 * It never reads, logs or returns a credential or a plaintext token.
 */
final class UserAccessRevocationService
{
    public function revoke(AccessRevocationCommand $command): AccessRevocationResult
    {
        $user = $this->resolveUser($command);

        try {
            return DB::transaction(function () use ($command, $user): AccessRevocationResult {
                $sessionsDeleted = 0;
                if ($command->revokeSessions) {
                    $sessionQuery = DB::table((string) config('session.table', 'sessions'))
                        ->where('user_id', (string) $user->getKey());

                    if ($command->exceptSessionId !== null && trim($command->exceptSessionId) !== '') {
                        $sessionQuery->where('id', '!=', $command->exceptSessionId);
                    }

                    $sessionsDeleted = $sessionQuery->delete();
                }

                $rememberTokenRevoked = false;
                if ($command->revokeRememberToken) {
                    // Nulling the stored token invalidates every remember-me
                    // cookie without creating or persisting a new secret.
                    $user->forceFill(['remember_token' => null])->save();
                    $rememberTokenRevoked = true;
                }

                $apiTokensRevoked = 0;
                if ($command->revokeApiTokens) {
                    $apiTokensRevoked = app(ApiTokenService::class)->revokeAllForUser($user);
                }

                $passwordResetTokensDeleted = DB::table('password_reset_tokens')
                    ->where('email', $user->email)
                    ->delete();

                $invitationsRevoked = 0;
                if ($command->revokeInvitations) {
                    $invitations = UserInvitation::query()
                        ->where('user_id', $user->getKey())
                        ->whereNull('consumed_at')
                        ->whereNull('revoked_at')
                        ->get();

                    foreach ($invitations as $invitation) {
                        if ($invitation->revoke()) {
                            $invitationsRevoked++;
                        }
                    }
                }

                $setupTokensRevoked = 0;
                if ($command->revokeInvitations) {
                    // The legacy setup-token table has no `revoked_at`; marking
                    // an unused token as used is its single-use invalidation.
                    $setupTokens = SchoolAdminSetupToken::query()
                        ->where('user_id', $user->getKey())
                        ->whereNull('used_at')
                        ->get();

                    foreach ($setupTokens as $setupToken) {
                        $setupToken->forceFill(['used_at' => now()])->save();
                        $setupTokensRevoked++;
                    }
                }

                $permissionCacheCleared = false;
                if ($command->revokePermissionCache) {
                    $user->forgetCachedPermissions();
                    app(PermissionRegistrar::class)->forgetCachedPermissions();
                    $permissionCacheCleared = true;
                }

                return new AccessRevocationResult(
                    $user->refresh(),
                    $command->trigger,
                    $sessionsDeleted,
                    $rememberTokenRevoked,
                    $apiTokensRevoked,
                    $passwordResetTokensDeleted,
                    $invitationsRevoked,
                    $setupTokensRevoked,
                    $permissionCacheCleared,
                );
            });
        } catch (AccessRevocationException $exception) {
            throw $exception;
        } catch (Throwable) {
            throw new AccessRevocationException(AccessRevocationFailure::Persistence);
        }
    }

    private function resolveUser(AccessRevocationCommand $command): User
    {
        /** @var User|null $user */
        $user = User::withTrashed()->find($command->user->getKey());

        if (! $user instanceof User) {
            throw new AccessRevocationException(AccessRevocationFailure::Target);
        }

        $expectedSchoolId = $command->expectedSchoolId === null
            ? null
            : trim($command->expectedSchoolId);
        $actualSchoolId = $user->school_id === null ? '' : trim((string) $user->school_id);

        if ($expectedSchoolId !== null && $expectedSchoolId !== '' && $expectedSchoolId !== $actualSchoolId) {
            throw new AccessRevocationException(AccessRevocationFailure::Tenant);
        }

        return $user;
    }
}
