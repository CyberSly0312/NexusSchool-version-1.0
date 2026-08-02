<?php

declare(strict_types=1);

namespace App\Domains\Academic\Application\Actions;

use App\Domains\Academic\Application\DTOs\CalendarTermData;
use App\Domains\Academic\Domain\Contracts\CalendarYearRepositoryInterface;
use App\Domains\Academic\Domain\Events\CalendarTermCreated;
use App\Domains\Academic\Domain\Exceptions\OverlappingCalendarTermException;
use App\Domains\Academic\Domain\Exceptions\TermOutsideCalendarYearException;
use App\Domains\Academic\Domain\ValueObjects\DateRange;
use App\Domains\Academic\Infrastructure\Models\AcademicCalendarTerm;
use App\Domains\Academic\Infrastructure\Models\AcademicCalendarYear;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class CreateCalendarTermAction
{
    public function __construct(
        private readonly CalendarYearRepositoryInterface $calendarYears,
    ) {
    }

    public function execute(CalendarTermData $data, ?User $actor = null): AcademicCalendarTerm
    {
        $calendarYear = AcademicCalendarYear::query()->findOrFail($data->calendarYearId);

        $termRange = DateRange::fromStrings($data->startsOn, $data->endsOn);
        $yearRange = DateRange::fromStrings(
            $calendarYear->start_date->toDateString(),
            $calendarYear->end_date->toDateString(),
        );

        if (! $yearRange->contains($termRange)) {
            throw TermOutsideCalendarYearException::create($data->name, $calendarYear->name);
        }

        $overlap = $this->calendarYears->overlappingTerm(
            schoolId: $data->schoolId,
            calendarYearId: $data->calendarYearId,
            range: $termRange,
        );

        if ($overlap !== null) {
            throw OverlappingCalendarTermException::create($data->name);
        }

        return DB::transaction(function () use ($data, $actor): AcademicCalendarTerm {
            $term = AcademicCalendarTerm::create($data->toArray());

            CalendarTermCreated::dispatch($term, $actor);

            return $term;
        });
    }
}
