<?php

declare(strict_types=1);

namespace App\Domains\Guardian\Infrastructure\Policies;

use App\Domains\Guardian\Domain\Traits\HandlesGuardianAuthorization;

final class StudentGuardianRelationshipPolicy
{
    use HandlesGuardianAuthorization;

    protected function permissionGroup(): string
    {
        return 'guardian';
    }
}
