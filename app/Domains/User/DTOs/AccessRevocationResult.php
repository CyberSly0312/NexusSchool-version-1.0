<?php

declare(strict_types=1);

namespace App\Domains\User\DTOs;

use App\Domains\User\Enums\AccessRevocationTrigger;
use App\Models\User;

final readonly class AccessRevocationResult
{
    public function __construct(
        public User $user,
        public AccessRevocationTrigger $trigger,
        public int $sessionsDeleted,
        public bool $rememberTokenRevoked,
        public int $apiTokensRevoked,
        public int $passwordResetTokensDeleted,
        public int $invitationsRevoked,
        public int $setupTokensRevoked,
        public bool $permissionCacheCleared,
    ) {
    }

    /**
     * @return array<string, int|bool|string>
     */
    public function toAuditValues(): array
    {
        return [
            'trigger' => $this->trigger->value,
            'sessions_deleted' => $this->sessionsDeleted,
            'remember_token_revoked' => $this->rememberTokenRevoked,
            'api_tokens_revoked' => $this->apiTokensRevoked,
            'password_reset_tokens_deleted' => $this->passwordResetTokensDeleted,
            'invitations_revoked' => $this->invitationsRevoked,
            'setup_tokens_revoked' => $this->setupTokensRevoked,
            'permission_cache_cleared' => $this->permissionCacheCleared,
        ];
    }
}
