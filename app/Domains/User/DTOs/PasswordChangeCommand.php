<?php

declare(strict_types=1);

namespace App\Domains\User\DTOs;

use App\Models\User;

/**
 * The actor is also the target: a password change can never name another user.
 */
final readonly class PasswordChangeCommand
{
    public function __construct(
        public User $actor,
        public string $currentPassword,
        public string $newPassword,
        public string $newPasswordConfirmation,
        public ?string $currentSessionId = null,
    ) {
    }
}
