<?php

declare(strict_types=1);

namespace App\Domains\User\DTOs;

use App\Models\User;

final readonly class PasswordChangeResult
{
    public function __construct(
        public User $user,
        public AccessRevocationResult $revocation,
    ) {
    }
}
