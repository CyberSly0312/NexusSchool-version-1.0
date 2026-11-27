<?php

declare(strict_types=1);

namespace App\Domains\School\DTOs;

use App\Domains\User\DTOs\AccessRevocationResult;
use App\Models\User;

final readonly class SchoolAdminReplacementResult
{
    public function __construct(
        public User $formerAdmin,
        public User $replacement,
        public string $activationUrl,
        /**
         * Plaintext capability handed to the operator exactly once. It is never
         * persisted, audited or included in an exception.
         */
        public string $plainToken,
        public AccessRevocationResult $revocation,
    ) {
    }
}
