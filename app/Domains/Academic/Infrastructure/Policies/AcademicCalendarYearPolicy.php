<?php

declare(strict_types=1);

namespace App\Domains\Academic\Infrastructure\Policies;

use App\Domains\Academic\Domain\Traits\HandlesSchoolResourceAuthorization;
use App\Domains\Academic\Infrastructure\Models\AcademicCalendarYear;
use App\Models\User;

final class AcademicCalendarYearPolicy
{
    use HandlesSchoolResourceAuthorization;

    protected function permissionGroup(): string
    {
        return 'academic_calendar_year';
    }

    public function activate(User $user, AcademicCalendarYear $calendarYear): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $calendarYear->school_id
            && $user->hasPermissionTo('academic_calendar_year.update');
    }
}
