<?php

declare(strict_types=1);

namespace App\Domains\Academic\Infrastructure\Policies;

use App\Domains\Academic\Domain\Traits\HandlesSchoolResourceAuthorization;

final class EducationLevelPolicy
{
    use HandlesSchoolResourceAuthorization;

    protected function permissionGroup(): string
    {
        return 'education_level';
    }
}
