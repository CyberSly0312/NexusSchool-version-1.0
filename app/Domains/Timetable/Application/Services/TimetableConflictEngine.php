<?php

declare(strict_types=1);

namespace App\Domains\Timetable\Application\Services;

use App\Domains\Timetable\Application\DTOs\ConflictReportData;
use App\Domains\Timetable\Domain\Enums\ConflictType;
use App\Domains\Timetable\Domain\ValueObjects\TimeRange;
use App\Domains\Timetable\Infrastructure\Models\AvailabilityConstraint;
use App\Domains\Timetable\Infrastructure\Models\ScheduleEntry;
use App\Domains\Timetable\Infrastructure\Models\TeachingPeriod;
use App\Domains\Timetable\Infrastructure\Models\Timetable;
use Illuminate\Support\Collection;

/**
 * Deterministic conflict detector (SPR-007). Detects teacher / class / section /
 * room double-bookings, duplicate periods, availability-constraint violations
 * and out-of-grid periods. Pure computation: persistence is the caller's job.
 *
 * The detection order is fixed (entries sorted by class, day, time, id) so the
 * same input always yields the same report.
 */
final class TimetableConflictEngine
{
    /**
     * @param  list<ScheduleEntry>|null  $entries
     * @param  Collection<int, AvailabilityConstraint>|null  $constraints
     * @return list<ConflictReportData>
     */
    public function detect(Timetable $timetable, ?array $entries = null, ?Collection $constraints = null): array
    {
        $entries = $entries ?? $timetable->entries()->get()->all();
        $constraints = $constraints ?? $timetable->availabilityConstraints()->get();

        $active = array_values(array_filter(
            $entries,
            static fn (ScheduleEntry $entry): bool => $entry->isScheduled(),
        ));

        usort($active, static function (ScheduleEntry $a, ScheduleEntry $b): int {
            return [$a->school_class_id, $a->day_of_week, $a->period_number, $a->getKey()]
                <=> [$b->school_class_id, $b->day_of_week, $b->period_number, $b->getKey()];
        });

        $reports = [];
        $count = count($active);

        for ($i = 0; $i < $count; $i++) {
            $a = $active[$i];

            for ($j = $i + 1; $j < $count; $j++) {
                $b = $active[$j];

                if ($a->day_of_week !== $b->day_of_week) {
                    continue;
                }

                $overlap = (new TimeRange((string) $a->starts_at, (string) $a->ends_at))
                    ->overlaps(new TimeRange((string) $b->starts_at, (string) $b->ends_at));

                $duplicatePeriod = $a->period_number === $b->period_number;

                if ($a->school_class_id === $b->school_class_id) {
                    if ($duplicatePeriod) {
                        $reports[] = $this->pairReport(
                            ConflictType::DuplicatePeriod,
                            $timetable,
                            $a,
                            $b,
                            'La classe est planifiée deux fois dans la même période.',
                        );
                    } elseif ($overlap) {
                        $reports[] = $this->pairReport(
                            ConflictType::ClassConflict,
                            $timetable,
                            $a,
                            $b,
                            'La classe est réservée sur deux créneaux qui se chevauchent.',
                        );
                    }
                }

                if ($overlap && $a->class_section_id !== null && $a->class_section_id === $b->class_section_id) {
                    $reports[] = $this->pairReport(
                        ConflictType::SectionConflict,
                        $timetable,
                        $a,
                        $b,
                        'La section est réservée sur deux créneaux qui se chevauchent.',
                    );
                }

                if ($overlap && $a->staff_id !== null && $a->staff_id === $b->staff_id) {
                    $reports[] = $this->pairReport(
                        ConflictType::TeacherConflict,
                        $timetable,
                        $a,
                        $b,
                        'L\'enseignant est planifié sur deux cours simultanés.',
                    );
                }

                if ($overlap && $a->room_id !== null && $a->room_id === $b->room_id) {
                    $reports[] = $this->pairReport(
                        ConflictType::RoomConflict,
                        $timetable,
                        $a,
                        $b,
                        'La salle est réservée pour deux cours simultanés.',
                    );
                }
            }
        }

        $reports = array_merge(
            $reports,
            $this->detectConstraintViolations($timetable, $active, $constraints),
            $this->detectOutOfGrid($timetable, $active),
        );

        return $reports;
    }

    /**
     * @param  list<ScheduleEntry>  $entries
     * @param  Collection<int, AvailabilityConstraint>  $constraints
     * @return list<ConflictReportData>
     */
    private function detectConstraintViolations(Timetable $timetable, array $entries, Collection $constraints): array
    {
        $reports = [];

        foreach ($entries as $entry) {
            if ($entry->staff_id !== null) {
                foreach ($constraints as $constraint) {
                    if ($constraint->constraint_type->value !== 'teacher') {
                        continue;
                    }

                    if ($constraint->target_id !== (string) $entry->staff_id) {
                        continue;
                    }

                    if (! $constraint->blocks((string) $entry->starts_at, (int) $entry->day_of_week)) {
                        continue;
                    }

                    $reports[] = $this->singleReport(
                        ConflictType::UnavailableTeacher,
                        $timetable,
                        $entry,
                        'L\'enseignant est indisponible sur ce créneau.',
                        'teacher',
                        (string) $entry->staff_id,
                    );
                }
            }

            if ($entry->room_id !== null) {
                foreach ($constraints as $constraint) {
                    if ($constraint->constraint_type->value !== 'room') {
                        continue;
                    }

                    if ($constraint->target_id !== $entry->room_id) {
                        continue;
                    }

                    if (! $constraint->blocks((string) $entry->starts_at, (int) $entry->day_of_week)) {
                        continue;
                    }

                    $reports[] = $this->singleReport(
                        ConflictType::UnavailableRoom,
                        $timetable,
                        $entry,
                        'La salle est indisponible sur ce créneau.',
                        'room',
                        $entry->room_id,
                    );
                }
            }
        }

        return $reports;
    }

    /**
     * @param  list<ScheduleEntry>  $entries
     * @return list<ConflictReportData>
     */
    private function detectOutOfGrid(Timetable $timetable, array $entries): array
    {
        $grid = TeachingPeriod::query()
            ->forSchool($timetable->school_id)
            ->where('is_active', true)
            ->get()
            ->groupBy('day_of_week');

        $reports = [];

        foreach ($entries as $entry) {
            $daySlots = $grid->get($entry->day_of_week, collect());

            $matches = $daySlots->contains(function (TeachingPeriod $period) use ($entry): bool {
                return (string) $period->starts_at === (string) $entry->starts_at
                    && (string) $period->ends_at === (string) $entry->ends_at;
            });

            if (! $matches) {
                $reports[] = $this->singleReport(
                    ConflictType::UnavailablePeriod,
                    $timetable,
                    $entry,
                    'Le créneau ne correspond à aucune période d\'enseignement active.',
                );
            }
        }

        return $reports;
    }

    private function pairReport(
        ConflictType $type,
        Timetable $timetable,
        ScheduleEntry $a,
        ScheduleEntry $b,
        string $reason,
    ): ConflictReportData {
        return new ConflictReportData(
            type: $type,
            reason: $reason,
            timetableId: $timetable->getKey(),
            entryId: (int) $a->getKey(),
            conflictingEntryId: (int) $b->getKey(),
            dayOfWeek: (int) $a->day_of_week,
            startsAt: (string) $a->starts_at,
            endsAt: (string) $a->ends_at,
        );
    }

    private function singleReport(
        ConflictType $type,
        Timetable $timetable,
        ScheduleEntry $entry,
        string $reason,
        ?string $targetType = null,
        ?string $targetId = null,
    ): ConflictReportData {
        return new ConflictReportData(
            type: $type,
            reason: $reason,
            timetableId: $timetable->getKey(),
            entryId: (int) $entry->getKey(),
            targetType: $targetType,
            targetId: $targetId,
            dayOfWeek: (int) $entry->day_of_week,
            startsAt: (string) $entry->starts_at,
            endsAt: (string) $entry->ends_at,
        );
    }
}
