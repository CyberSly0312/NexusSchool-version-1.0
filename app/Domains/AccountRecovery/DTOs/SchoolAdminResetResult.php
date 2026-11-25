<?php

declare(strict_types=1);

namespace App\Domains\AccountRecovery\DTOs;

use App\Models\User;
use App\Models\UserInvitation;
use Illuminate\Support\Carbon;

/**
 * The plaintext token exists only in this transient result. It is never placed
 * in an audit record, exception message, log entry or persisted model field.
 */
final readonly class SchoolAdminResetResult
{
    public function __construct(
        public User $target,
        public UserInvitation $invitation,
        public string $plainToken,
        public string $resetUrl,
        public Carbon $expiresAt,
    ) {
    }
}
