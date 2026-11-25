<?php

declare(strict_types=1);

namespace App\Domains\School\DTOs;

use App\Domains\School\Models\School;
use App\Models\User;

final readonly class SchoolAdminLifecycleCommand
{
    public function __construct(
        public User $actor,
        public School $school,
        public string $targetUserId,
    ) {
    }
}
