<?php

declare(strict_types=1);

namespace App\Domains\School\DTOs;

use App\Domains\School\Models\School;
use App\Models\User;

final readonly class SchoolAdminReplacementCommand
{
    public function __construct(
        public User $actor,
        public School $school,
        public string $targetUserId,
        public string $firstName,
        public string $lastName,
        public string $email,
        public ?string $phone = null,
    ) {
    }
}
