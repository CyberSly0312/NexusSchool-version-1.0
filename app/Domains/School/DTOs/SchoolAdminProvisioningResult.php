<?php

declare(strict_types=1);

namespace App\Domains\School\DTOs;

use App\Models\User;
use Illuminate\Support\Carbon;

/**
 * Result of provisioning or recovering a school administrator.
 *
 * The setup URL (and therefore the plaintext token) is present only when a new
 * one-time setup link was created during this call. It must be handed to the
 * administrator through a secure channel and is never persisted in plaintext.
 */
readonly class SchoolAdminProvisioningResult
{
    public function __construct(
        public User $user,
        public bool $created,
        public ?string $setupUrl = null,
        public ?Carbon $expiresAt = null,
    ) {
    }
}
