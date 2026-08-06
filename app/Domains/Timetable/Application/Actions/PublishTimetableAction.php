<?php

declare(strict_types=1);

namespace App\Domains\Timetable\Application\Actions;

use App\Domains\Timetable\Application\DTOs\PublishTimetableData;
use App\Domains\Timetable\Domain\Events\TimetablePublished;
use App\Domains\Timetable\Domain\Exceptions\InvalidTimetableStateException;
use App\Domains\Timetable\Domain\Exceptions\ScheduleEntryException;
use App\Domains\Timetable\Infrastructure\Models\Timetable;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Publishes a draft timetable. Publication creates an immutable version snapshot
 * (TimetableVersion), bumps the version counter and records the publish time.
 * Published schedules are never overwritten: only one published timetable is
 * allowed per academic period, so the previous published timetable must be
 * archived first.
 */
final class PublishTimetableAction
{
    public function execute(int|string $timetableId, ?User $actor = null, ?PublishTimetableData $data = null): Timetable
    {
        $timetable = Timetable::query()->find($timetableId);

        if ($timetable === null) {
            throw new InvalidTimetableStateException('Timetable not found.');
        }

        if (! $timetable->isDraft()) {
            throw new InvalidTimetableStateException('Only a draft timetable can be published.');
        }

        $scheduledCount = $timetable->entries()->where('status', 'scheduled')->count();

        if ($scheduledCount === 0) {
            throw new ScheduleEntryException('A timetable must contain at least one scheduled entry before being published.');
        }

        $this->assertNoOtherPublished($timetable);

        if ((bool) config('nexus-timetable.require_conflict_free_publish', false)) {
            $this->assertConflictFree($timetable);
        }

        return DB::transaction(function () use ($timetable, $actor): Timetable {
            $version = (int) $timetable->version + 1;

            $timetable->update([
                'status' => 'published',
                'version' => $version,
                'total_entries' => $timetable->entries()->where('status', 'scheduled')->count(),
                'published_at' => now(),
                'archived_at' => null,
            ]);

            TimetablePublished::dispatch($timetable->refresh(), $version, $actor);

            return $timetable->refresh();
        });
    }

    private function assertNoOtherPublished(Timetable $timetable): void
    {
        $existing = Timetable::query()
            ->forSchool($timetable->school_id)
            ->where('status', 'published')
            ->whereKeyNot($timetable->getKey())
            ->when($timetable->academic_calendar_year_id !== null, function ($query) use ($timetable) {
                $query->where('academic_calendar_year_id', $timetable->academic_calendar_year_id);
            })
            ->when($timetable->academic_calendar_term_id !== null, function ($query) use ($timetable) {
                $query->where('academic_calendar_term_id', $timetable->academic_calendar_term_id);
            })
            ->exists();

        if ($existing) {
            throw new InvalidTimetableStateException(
                'Only one timetable per academic period may be published. Archive the current published timetable first.'
            );
        }
    }

    private function assertConflictFree(Timetable $timetable): void
    {
        $engine = app(\App\Domains\Timetable\Application\Services\TimetableConflictEngine::class);

        if ($engine->detect($timetable) !== []) {
            throw new ScheduleEntryException('This timetable has unresolved conflicts and cannot be published.');
        }
    }
}
