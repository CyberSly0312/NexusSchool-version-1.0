<?php

declare(strict_types=1);

namespace App\Domains\Academic\Application\Actions;

use App\Domains\Academic\Application\DTOs\CalendarYearData;
use App\Domains\Academic\Domain\Events\CalendarYearCreated;
use App\Domains\Academic\Domain\Exceptions\DuplicateResourceException;
use App\Domains\Academic\Infrastructure\Models\AcademicCalendarYear;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class CreateCalendarYearAction
{
    public function execute(CalendarYearData $data, ?User $actor = null): AcademicCalendarYear
    {
        $duplicate = AcademicCalendarYear::query()
            ->forSchool($data->schoolId)
            ->where('name', $data->name)
            ->withTrashed()
            ->exists();

        if ($duplicate) {
            throw DuplicateResourceException::for('calendar year', 'name', $data->name);
        }

        return DB::transaction(function () use ($data, $actor): AcademicCalendarYear {
            $year = AcademicCalendarYear::create($data->toArray());

            CalendarYearCreated::dispatch($year, $actor);

            return $year;
        });
    }
}
