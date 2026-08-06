<?php

declare(strict_types=1);

namespace App\Domains\Timetable\Application\Services;

use App\Domains\Timetable\Application\Actions\ArchiveTimetableAction;
use App\Domains\Timetable\Application\Actions\AssignRoomAction;
use App\Domains\Timetable\Application\Actions\AssignTeacherAction;
use App\Domains\Timetable\Application\Actions\CreateAvailabilityConstraintAction;
use App\Domains\Timetable\Application\Actions\CreateScheduleEntryAction;
use App\Domains\Timetable\Application\Actions\CreateTimetableAction;
use App\Domains\Timetable\Application\Actions\DetectConflictsAction;
use App\Domains\Timetable\Application\Actions\DuplicateTimetableAction;
use App\Domains\Timetable\Application\Actions\GenerateTimetableAction;
use App\Domains\Timetable\Application\Actions\PublishTimetableAction;
use App\Domains\Timetable\Application\Actions\ResolveConflictAction;
use App\Domains\Timetable\Application\Actions\UpdateScheduleEntryAction;
use App\Domains\Timetable\Application\DTOs\AvailabilityConstraintData;
use App\Domains\Timetable\Application\DTOs\CreateTimetableData;
use App\Domains\Timetable\Application\DTOs\GenerateTimetableData;
use App\Domains\Timetable\Application\DTOs\PublishTimetableData;
use App\Domains\Timetable\Application\DTOs\ScheduleEntryData;
use App\Domains\Timetable\Application\DTOs\UpdateScheduleEntryData;
use App\Domains\Timetable\Infrastructure\Models\AvailabilityConstraint;
use App\Domains\Timetable\Infrastructure\Models\ScheduleEntry;
use App\Domains\Timetable\Infrastructure\Models\Timetable;
use App\Domains\Timetable\Infrastructure\Models\TimetableConflict;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Application facade of the Timetable engine (SPR-007). Every domain operation
 * is exposed here as a single entry point; actions stay thin and testable.
 */
final class TimetableService
{
    public function __construct(
        private readonly CreateTimetableAction $createTimetable,
        private readonly GenerateTimetableAction $generateTimetable,
        private readonly PublishTimetableAction $publishTimetable,
        private readonly ArchiveTimetableAction $archiveTimetable,
        private readonly DuplicateTimetableAction $duplicateTimetable,
        private readonly DetectConflictsAction $detectConflicts,
        private readonly AssignTeacherAction $assignTeacher,
        private readonly AssignRoomAction $assignRoom,
        private readonly UpdateScheduleEntryAction $updateScheduleEntry,
        private readonly CreateScheduleEntryAction $createScheduleEntry,
        private readonly CreateAvailabilityConstraintAction $createAvailabilityConstraint,
        private readonly ResolveConflictAction $resolveConflict,
        private readonly TimetableQueryService $query,
    ) {
    }

    public function createTimetable(CreateTimetableData $data, ?User $actor = null): Timetable
    {
        return $this->createTimetable->execute($data, $actor);
    }

    public function generate(GenerateTimetableData $data, ?User $actor = null): Timetable
    {
        return $this->generateTimetable->execute($data, $actor);
    }

    public function publish(int|string $timetableId, ?User $actor = null, ?PublishTimetableData $data = null): Timetable
    {
        return $this->publishTimetable->execute($timetableId, $actor, $data);
    }

    public function archive(int|string $timetableId, ?User $actor = null): Timetable
    {
        return $this->archiveTimetable->execute($timetableId, $actor);
    }

    public function duplicate(int|string $timetableId, ?User $actor = null): Timetable
    {
        return $this->duplicateTimetable->execute($timetableId, $actor);
    }

    public function detectConflicts(int|string $timetableId, ?User $actor = null): Collection
    {
        return $this->detectConflicts->execute($timetableId, $actor);
    }

    public function assignTeacher(int|string $entryId, int|string $staffId, ?User $actor = null): ScheduleEntry
    {
        return $this->assignTeacher->execute($entryId, $staffId, $actor);
    }

    public function assignRoom(int|string $entryId, string $roomId, ?User $actor = null): ScheduleEntry
    {
        return $this->assignRoom->execute($entryId, $roomId, $actor);
    }

    public function updateEntry(UpdateScheduleEntryData $data, ?User $actor = null): ScheduleEntry
    {
        return $this->updateScheduleEntry->execute($data, $actor);
    }

    public function createEntry(ScheduleEntryData $data, ?User $actor = null): ScheduleEntry
    {
        return $this->createScheduleEntry->execute($data, $actor);
    }

    public function addConstraint(AvailabilityConstraintData $data, ?User $actor = null): AvailabilityConstraint
    {
        return $this->createAvailabilityConstraint->execute($data, $actor);
    }

    public function resolveConflict(int|string $conflictId, string $resolution = 'resolved', ?string $notes = null, ?User $actor = null): TimetableConflict
    {
        return $this->resolveConflict->execute($conflictId, $resolution, $notes, $actor);
    }

    public function query(): TimetableQueryService
    {
        return $this->query;
    }
}
