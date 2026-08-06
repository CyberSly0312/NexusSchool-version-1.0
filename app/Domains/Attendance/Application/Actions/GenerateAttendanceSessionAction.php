<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Application\Actions;

use App\Domains\Attendance\Application\DTOs\SessionGenerationData;
use App\Domains\Attendance\Domain\Contracts\AcademicFrameworkContract;
use App\Domains\Attendance\Domain\Contracts\EnrollmentContract;
use App\Domains\Attendance\Domain\Contracts\TimetableContract;
use App\Domains\Attendance\Domain\Events\AttendanceSessionCreated;
use App\Domains\Attendance\Domain\Exceptions\ContractViolationException;
use App\Domains\Attendance\Domain\Exceptions\SessionAlreadyExistsException;
use App\Domains\Attendance\Domain\References\TimeSlotReference;
use App\Domains\Attendance\Infrastructure\Models\AttendanceSession;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Generates one Draft AttendanceSession per class + time slot on the requested
 * dates (SPR-008). Slots come from the TimetableContract, the cohort size from
 * the EnrollmentContract — the engine never touches those domains' models.
 *
 * @return list<AttendanceSession>
 */
final class GenerateAttendanceSessionAction
{
    public function __construct(
        private readonly AcademicFrameworkContract $academicFramework,
        private readonly EnrollmentContract $enrollment,
        private readonly TimetableContract $timetable,
    ) {
    }

    public function execute(SessionGenerationData $data, ?User $actor = null): array
    {
        $this->validateClass($data);

        $dates = $data->dates();

        if ($dates === []) {
            throw new ContractViolationException('No dates provided for session generation.');
        }

        $cohortSize = count($this->enrollment->enrollmentsOfClass($data->schoolId, $data->classId, $data->academicYearId));

        $sessions = [];

        DB::transaction(function () use ($data, $dates, $cohortSize, $actor, &$sessions): void {
            foreach ($dates as $date) {
                foreach ($this->timetable->slotsForClassOn($data->schoolId, $data->classId, $date) as $slot) {
                    $sessions[] = $this->createForSlot($data, $slot, $date, $cohortSize, $actor);
                }
            }
        });

        return $sessions;
    }

    private function validateClass(SessionGenerationData $data): void
    {
        $class = $this->academicFramework->class($data->schoolId, $data->classId);

        if ($class === null) {
            throw new ContractViolationException('The school class does not exist for this school.');
        }

        if ($data->academicYearId !== null && $this->academicFramework->year($data->schoolId, $data->academicYearId) === null) {
            throw new ContractViolationException('The academic year does not exist for this school.');
        }

        if ($data->academicTermId !== null && $this->academicFramework->term($data->schoolId, $data->academicTermId) === null) {
            throw new ContractViolationException('The academic term does not exist for this school.');
        }
    }

    private function createForSlot(
        SessionGenerationData $data,
        TimeSlotReference $slot,
        string $date,
        int $cohortSize,
        ?User $actor,
    ): AttendanceSession {
        $existing = AttendanceSession::query()
            ->forSchool($data->schoolId)
            ->where('school_class_id', $data->classId)
            ->whereDate('date', $date)
            ->where('day_of_week', $slot->dayOfWeek)
            ->where('period_number', $slot->periodNumber)
            ->exists();

        if ($existing) {
            throw SessionAlreadyExistsException::forSlot($data->classId, $date, $slot->periodNumber);
        }

        $session = AttendanceSession::create([
            'school_id' => $data->schoolId,
            'academic_calendar_year_id' => $data->academicYearId,
            'academic_calendar_term_id' => $data->academicTermId,
            'school_class_id' => $data->classId,
            'class_section_id' => $slot->sectionId,
            'academic_subject_id' => $slot->subjectId,
            'timetable_id' => $data->timetableId,
            'schedule_entry_id' => $slot->id,
            'date' => $date,
            'day_of_week' => $slot->dayOfWeek,
            'period_number' => $slot->periodNumber,
            'starts_at' => $slot->startsAt,
            'ends_at' => $slot->endsAt,
            'staff_id' => $slot->staffId,
            'status' => 'draft',
            'expected_students_count' => $cohortSize,
            'created_by' => $actor?->getKey(),
        ]);

        AttendanceSessionCreated::dispatch($session, $actor);

        return $session;
    }
}
