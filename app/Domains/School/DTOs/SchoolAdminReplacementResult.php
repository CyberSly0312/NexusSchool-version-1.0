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
        public AccessRevocationResult $revocation,
    ) {
    }
}
