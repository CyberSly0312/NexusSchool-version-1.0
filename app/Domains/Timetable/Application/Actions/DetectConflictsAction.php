<?php

declare(strict_types=1);

namespace App\Domains\Timetable\Application\Actions;

use App\Domains\Timetable\Application\Services\TimetableConflictEngine;
use App\Domains\Timetable\Domain\Contracts\AcademicFrameworkContract;
use App\Domains\Timetable\Domain\Enums\ConflictType;
use App\Domains\Timetable\Domain\Events\ConflictDetected;
use App\Domains\Timetable\Domain\Exceptions\InvalidTimetableStateException;
use App\Domains\Timetable\Domain\ValueObjects\ConflictReason;
use App\Domains\Timetable\Infrastructure\Models\Timetable;
use App\Domains\Timetable\Infrastructure\Models\TimetableConflict;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Runs the conflict engine over a timetable and persists the findings as
 * immutable evidence rows tagged with a detection id. A closed academic term is
 * additionally reported as an unavailable-period conflict through the academic
 * framework contract.
 */
final class DetectConflictsAction
{
    public function __construct(
        private readonly TimetableConflictEngine $engine,
        private readonly AcademicFrameworkContract $academicFramework,
    ) {
    }

    public function execute(int|string $timetableId, ?User $actor = null): Collection
    {
        $timetable = Timetable::query()->find($timetableId);

        if ($timetable === null) {
            throw new InvalidTimetableStateException('Timetable not found.');
        }

        $reports = $this->engine->detect($timetable);

        $detectionId = (string) Str::uuid();

        $conflicts = DB::transaction(function () use ($timetable, $reports, $detectionId, $actor): Collection {
            $rows = [];

            foreach ($reports as $report) {
                $rows[] = TimetableConflict::create([
                    'school_id' => $timetable->school_id,
                    'timetable_id' => $timetable->getKey(),
                    'detection_id' => $detectionId,
                    'conflict_type' => $report->type->value,
                    'entry_id' => $report->entryId,
                    'conflicting_entry_id' => $report->conflictingEntryId,
                    'target_type' => $report->targetType,
                    'target_id' => $report->targetId,
                    'day_of_week' => $report->dayOfWeek,
                    'starts_at' => $report->startsAt,
                    'ends_at' => $report->endsAt,
                    'reason' => $report->reason,
                ]);
            }

            $rows = array_merge($rows, $this->persistClosedTerm($timetable, $detectionId));

            ConflictDetected::dispatch($timetable, $rows, $detectionId, $actor);

            return collect($rows);
        });

        return $conflicts;
    }

    /**
     * @return list<TimetableConflict>
     */
    private function persistClosedTerm(Timetable $timetable, string $detectionId): array
    {
        if ($timetable->academic_calendar_term_id === null) {
            return [];
        }

        $term = $this->academicFramework->term($timetable->school_id, $timetable->academic_calendar_term_id);

        if ($term === null || $term->isOpen) {
            return [];
        }

        $reason = new ConflictReason(
            ConflictType::UnavailablePeriod,
            'La période académique est fermée : aucune leçon ne peut y être planifiée.',
        );

        return $timetable->entries()
            ->where('status', 'scheduled')
            ->get()
            ->map(function ($entry) use ($timetable, $detectionId, $reason): TimetableConflict {
                return TimetableConflict::create([
                    'school_id' => $timetable->school_id,
                    'timetable_id' => $timetable->getKey(),
                    'detection_id' => $detectionId,
                    'conflict_type' => $reason->type->value,
                    'entry_id' => $entry->getKey(),
                    'day_of_week' => $entry->day_of_week,
                    'starts_at' => $entry->starts_at,
                    'ends_at' => $entry->ends_at,
                    'reason' => $reason->__toString(),
                ]);
            })
            ->all();
    }
}
