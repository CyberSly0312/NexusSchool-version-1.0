<?php

declare(strict_types=1);

namespace App\Domains\User\Services;

use App\Domains\School\Models\School;
use App\Domains\User\DTOs\AccountInvitationActivationResult;
use App\Domains\User\Enums\UserStatus;
use App\Domains\User\Exceptions\AccountInvitationActivationException;
use App\Models\User;
use App\Models\UserInvitation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AccountInvitationActivationService
{
    /**
     * @var list<string>
     */
    private const ALLOWED_PURPOSES = [
        UserInvitation::PURPOSE_SCHOOL_ADMIN_INITIAL,
        UserInvitation::PURPOSE_STAFF_INITIAL,
        UserInvitation::PURPOSE_GUARDIAN_INITIAL,
        UserInvitation::PURPOSE_STUDENT_INITIAL,
    ];

    /**
     * Activate a pending user.
     *
     * When an expected school is supplied (the host-resolved tenant for HTTP
     * activation), the invitation is rejected unless it belongs to that exact
     * school. The check runs inside the same row-locked transaction that
     * consumes the invitation, so validation and consumption cannot diverge.
     */
    public function activate(
        string $plainToken,
        string $password,
        ?School $expectedSchool = null,
    ): AccountInvitationActivationResult {
        $this->assertPasswordPolicy($password);

        $plainToken = trim($plainToken);
        if ($plainToken === '') {
            throw AccountInvitationActivationException::invalidInvitation();
        }

        $tokenHash = UserInvitation::hashToken($plainToken);

        $activatedUser = DB::transaction(function () use ($tokenHash, $password, $expectedSchool): User {
            $now = now();

            /** @var UserInvitation|null $invitation */
            $invitation = UserInvitation::query()
                ->where('token_hash', $tokenHash)
                ->lockForUpdate()
                ->first();

            if (! $invitation instanceof UserInvitation) {
                throw AccountInvitationActivationException::invalidInvitation();
            }

            $this->assertExpectedSchoolMatches($invitation, $expectedSchool);
            $this->assertInvitationIsActivatable($invitation, $now);

            /** @var User|null $user */
            $user = User::query()
                ->whereKey($invitation->user_id)
                ->lockForUpdate()
                ->first();

            if (! $user instanceof User) {
                throw AccountInvitationActivationException::invalidInvitation();
            }

            $this->assertUserIsActivatable($user, $invitation);

            $school = School::query()->whereKey($invitation->school_id)->first();
            if (! $school instanceof School) {
                throw AccountInvitationActivationException::invalidInvitation();
            }

            $this->assertSchoolMatches($user, $invitation, $school);

            $user->forceFill([
                'password' => Hash::make($password),
                'status' => UserStatus::Active,
            ])->save();

            $freshInvitation = UserInvitation::query()
                ->whereKey($invitation->getKey())
                ->lockForUpdate()
                ->first();

            if (! $freshInvitation instanceof UserInvitation || ! $freshInvitation->isUsable($now)) {
                throw AccountInvitationActivationException::invalidInvitation();
            }

            $freshInvitation->forceFill(['consumed_at' => $now])->save();

            return $user->refresh();
        });

        return new AccountInvitationActivationResult($activatedUser);
    }

    /**
     * Read-only lookup used by the public activation page. Consumption always
     * goes through {@see self::activate()} so the school binding is rechecked
     * inside the activation transaction.
     */
    public function findUsableForSchool(string $plainToken, School $expectedSchool): ?UserInvitation
    {
        $plainToken = trim($plainToken);
        if ($plainToken === '') {
            return null;
        }

        $invitation = UserInvitation::findUsableByPlainToken($plainToken);
        if (! $invitation instanceof UserInvitation) {
            return null;
        }

        $expectedSchoolId = (string) $expectedSchool->getKey();
        $invitationSchoolId = (string) $invitation->school_id;

        if ($expectedSchoolId === '' || $invitationSchoolId === '' || $expectedSchoolId !== $invitationSchoolId) {
            return null;
        }

        return $invitation;
    }

    private function assertPasswordPolicy(string $password): void
    {
        $validator = Validator::make(
            ['password' => $password],
            ['password' => ['required', 'string', 'min:8']],
        );

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    private function assertInvitationIsActivatable(UserInvitation $invitation, Carbon $now): void
    {
        if (! in_array($invitation->purpose, self::ALLOWED_PURPOSES, true)) {
            throw AccountInvitationActivationException::invalidInvitation();
        }

        if ($invitation->isConsumed() || $invitation->isRevoked() || $invitation->isExpired($now)) {
            throw AccountInvitationActivationException::invalidInvitation();
        }
    }

    private function assertUserIsActivatable(User $user, UserInvitation $invitation): void
    {
        if ($user->status !== UserStatus::Pending) {
            throw AccountInvitationActivationException::invalidInvitation();
        }

        $invitationUserId = (string) $invitation->user_id;
        if ((string) $user->getKey() !== $invitationUserId) {
            throw AccountInvitationActivationException::invalidInvitation();
        }
    }

    private function assertExpectedSchoolMatches(UserInvitation $invitation, ?School $expectedSchool): void
    {
        if (! $expectedSchool instanceof School) {
            return;
        }

        $expectedSchoolId = (string) $expectedSchool->getKey();
        $invitationSchoolId = (string) $invitation->school_id;

        if ($expectedSchoolId === '' || $invitationSchoolId === '' || $expectedSchoolId !== $invitationSchoolId) {
            throw AccountInvitationActivationException::invalidInvitation();
        }
    }

    private function assertSchoolMatches(User $user, UserInvitation $invitation, School $school): void
    {
        $invitationSchoolId = (string) $invitation->school_id;
        $userSchoolId = (string) $user->school_id;
        $schoolId = (string) $school->getKey();

        if (strlen($invitationSchoolId) < 1 || strlen($userSchoolId) < 1 || strlen($schoolId) < 1) {
            throw AccountInvitationActivationException::invalidInvitation();
        }

        if ($invitationSchoolId !== $userSchoolId || $invitationSchoolId !== $schoolId) {
            throw AccountInvitationActivationException::invalidInvitation();
        }
    }
}
