<?php

declare(strict_types=1);

namespace App\Domains\Academic\Infrastructure\Policies;

use App\Domains\Academic\Domain\Traits\HandlesSchoolResourceAuthorization;

final class AcademicSubjectPolicy
{
    use HandlesSchoolResourceAuthorization;

    protected function permissionGroup(): string
    {
        return 'academic_subject';
    }
}
