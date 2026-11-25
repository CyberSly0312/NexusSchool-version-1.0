<?php

declare(strict_types=1);

namespace App\Domains\AccountRecovery\DTOs;

use App\Domains\User\DTOs\AccessRevocationResult;
use App\Models\User;
use App\Models\UserInvitation;

final readonly class CompletedPasswordResetResult
{
    public function __construct(
        public User $user,
        public UserInvitation $invitation,
        public AccessRevocationResult $revocation,
    ) {
    }
}
