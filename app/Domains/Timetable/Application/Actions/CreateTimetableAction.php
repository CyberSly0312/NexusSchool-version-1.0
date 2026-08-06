<?php

declare(strict_types=1);

namespace App\Domains\Timetable\Application\Actions;

use App\Domains\Timetable\Application\DTOs\CreateTimetableData;
use App\Domains\Timetable\Domain\Contracts\AcademicFrameworkContract;
use App\Domains\Timetable\Domain\Events\TimetableCreated;
use App\Domains\Timetable\Domain\Exceptions\ContractViolationException;
use App\Domains\Timetable\Domain\Exceptions\InvalidTimetableStateException;
use App\Domains\Timetable\Infrastructure\Models\Timetable;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Creates a Timetable aggregate in draft state. The academic year/term are
 * validated through the AcademicFrameworkContract and are scoped to the school.
 */
final class CreateTimetableAction
{
    public function __construct(
        private readonly AcademicFrameworkContract $academicFramework,
    ) {
    }

    public function execute(CreateTimetableData $data, ?User $actor = null): Timetable
    {
        $this->validateName($data);
        $this->validatePeriod($data);

        return DB::transaction(function () use ($data, $actor): Timetable {
            $timetable = Timetable::create([
                ...$data->toArray(),
                'total_entries' => 0,
                'created_by' => $actor?->getKey(),
            ]);

            TimetableCreated::dispatch($timetable, $actor);

            return $timetable;
        });
    }

    private function validateName(CreateTimetableData $data): void
    {
        if (trim($data->name) === '') {
            throw new InvalidTimetableStateException('Timetable name must not be empty.');
        }

        $exists = Timetable::query()
            ->forSchool($data->schoolId)
            ->where('name', trim($data->name))
            ->where('status', '!=', 'archived')
            ->exists();

        if ($exists) {
            throw new InvalidTimetableStateException("A timetable named [{$data->name}] already exists for this school.");
        }
    }

    private function validatePeriod(CreateTimetableData $data): void
    {
        if ($data->academicYearId !== null) {
            $year = $this->academicFramework->year($data->schoolId, $data->academicYearId);

            if ($year === null) {
                throw new ContractViolationException('The academic year does not exist for this school.');
            }
        }

        if ($data->academicTermId !== null) {
            $term = $this->academicFramework->term($data->schoolId, $data->academicTermId);

            if ($term === null) {
                throw new ContractViolationException('The academic term does not exist for this school.');
            }
        }
    }
}
