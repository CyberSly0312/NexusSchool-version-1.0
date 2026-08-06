<?php

declare(strict_types=1);

namespace App\Domains\Timetable\Infrastructure\Policies;

use App\Domains\Timetable\Domain\Traits\HandlesTimetableAuthorization;

final class TimetablePolicy
{
    use HandlesTimetableAuthorization;

    protected function permissionGroup(): string
    {
        return 'timetable';
    }
}
