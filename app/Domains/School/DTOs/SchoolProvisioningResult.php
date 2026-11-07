<?php

declare(strict_types=1);

namespace App\Domains\School\DTOs;

use App\Domains\School\Models\School;
use App\Models\User;

/**
 * Result of creating a school together with its initial administrator.
 */
readonly class SchoolProvisioningResult
{
    public function __construct(
        public School $school,
        public SchoolAdminProvisioningResult $administrator,
        public ?User $actor = null,
    ) {
    }
}
