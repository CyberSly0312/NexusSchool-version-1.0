<?php

declare(strict_types=1);

namespace App\Domains\User\DTOs;

use App\Domains\User\Enums\AccessRevocationTrigger;
use App\Models\User;

final readonly class AccessRevocationCommand
{
    public function __construct(
        public User $user,
        public AccessRevocationTrigger $trigger,
        public bool $revokeSessions = true,
        public bool $revokeRememberToken = true,
        public bool $revokeApiTokens = true,
        public bool $revokeInvitations = true,
        public bool $revokePermissionCache = true,
        public ?string $expectedSchoolId = null,
        public ?string $exceptSessionId = null,
    ) {
    }

    public static function forPasswordChange(User $user, ?string $currentSessionId): self
    {
        return new self(
            user: $user,
            trigger: AccessRevocationTrigger::PasswordChange,
            revokeSessions: true,
            revokeRememberToken: true,
            revokeApiTokens: false,
            revokeInvitations: true,
            revokePermissionCache: true,
            expectedSchoolId: $user->school_id === null ? null : (string) $user->school_id,
            exceptSessionId: $currentSessionId,
        );
    }

    public static function forAdministrativeReset(User $user, string $schoolId): self
    {
        return new self(
            user: $user,
            trigger: AccessRevocationTrigger::AdministrativeReset,
            expectedSchoolId: $schoolId,
        );
    }

    public static function forSuspension(User $user, string $schoolId): self
    {
        return new self(
            user: $user,
            trigger: AccessRevocationTrigger::SchoolAdminSuspension,
            expectedSchoolId: $schoolId,
        );
    }
}
