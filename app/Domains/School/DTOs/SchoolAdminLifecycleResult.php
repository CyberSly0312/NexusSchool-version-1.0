<?php

declare(strict_types=1);

namespace App\Domains\School\DTOs;

use App\Domains\User\DTOs\AccessRevocationResult;
use App\Models\User;

final readonly class SchoolAdminLifecycleResult
{
    public function __construct(
        public User $formerAdmin,
        public AccessRevocationResult $revocation,
    ) {
    }
}
