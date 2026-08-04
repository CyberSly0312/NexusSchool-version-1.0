<?php

declare(strict_types=1);

namespace App\Domains\Enrollment\Infrastructure\Policies;

use App\Domains\Enrollment\Domain\Traits\HandlesEnrollmentAuthorization;

final class EnrollmentPolicy
{
    use HandlesEnrollmentAuthorization;

    protected function permissionGroup(): string
    {
        return 'enrollment';
    }
}
