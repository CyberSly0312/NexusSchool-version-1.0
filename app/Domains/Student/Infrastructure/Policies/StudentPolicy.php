<?php

declare(strict_types=1);

namespace App\Domains\Student\Infrastructure\Policies;

use App\Domains\Student\Domain\Traits\HandlesStudentAuthorization;

final class StudentPolicy
{
    use HandlesStudentAuthorization;

    protected function permissionGroup(): string
    {
        return 'student';
    }
}
