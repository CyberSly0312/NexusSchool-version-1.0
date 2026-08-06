<?php

declare(strict_types=1);

namespace App\Domains\Timetable\Application\Actions;

use App\Domains\Timetable\Domain\Events\TimetableArchived;
use App\Domains\Timetable\Domain\Exceptions\InvalidTimetableStateException;
use App\Domains\Timetable\Infrastructure\Models\Timetable;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Archives a timetable. Archival is terminal: the schedule stays readable and
 * auditable but can never be published again.
 */
final class ArchiveTimetableAction
{
    public function execute(int|string $timetableId, ?User $actor = null): Timetable
    {
        $timetable = Timetable::query()->find($timetableId);

        if ($timetable === null) {
            throw new InvalidTimetableStateException('Timetable not found.');
        }

        if ($timetable->isArchived()) {
            throw new InvalidTimetableStateException('This timetable is already archived.');
        }

        return DB::transaction(function () use ($timetable, $actor): Timetable {
            $timetable->update([
                'status' => 'archived',
                'archived_at' => now(),
            ]);

            TimetableArchived::dispatch($timetable->refresh(), $actor);

            return $timetable->refresh();
        });
    }
}
