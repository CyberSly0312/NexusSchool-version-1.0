<?php

declare(strict_types=1);

namespace App\Domains\AccountRecovery\DTOs;

use App\Domains\School\Models\School;

final readonly class CompletePasswordResetCommand
{
    public function __construct(
        public School $school,
        public string $token,
        public string $password,
        public string $passwordConfirmation,
    ) {
    }
}
