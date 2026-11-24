<?php

declare(strict_types=1);

namespace App\Domains\User\DTOs;

final readonly class AccountProvisioningCommand
{
    public function __construct(
        public string $schoolId,
        public string $firstName,
        public string $lastName,
        public string $email,
        public string $roleName,
        public ?string $phone = null,
    ) {
    }
}
