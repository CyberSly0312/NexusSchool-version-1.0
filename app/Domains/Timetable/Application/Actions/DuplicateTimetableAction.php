<?php

declare(strict_types=1);

namespace App\Domains\Timetable\Application\Actions;

use App\Domains\Timetable\Domain\Events\TimetableDuplicated;
use App\Domains\Timetable\Domain\Exceptions\InvalidTimetableStateException;
use App\Domains\Timetable\Infrastructure\Models\AvailabilityConstraint;
use App\Domains\Timetable\Infrastructure\Models\RecurringPattern;
use App\Domains\Timetable\Infrastructure\Models\ScheduleEntry;
use App\Domains\Timetable\Infrastructure\Models\TimeSlot;
use App\Domains\Timetable\Infrastructure\Models\Timetable;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Creates a new draft timetable that copies every entry, time slot, recurring
 * pattern and availability constraint of the source. The copy is always a draft
 * so it can be edited and published independently.
 */
final class DuplicateTimetableAction
{
    public function execute(int|string $timetableId, ?User $actor = null): Timetable
    {
        $source = Timetable::query()->find($timetableId);

        if ($source === null) {
            throw new InvalidTimetableStateException('Timetable not found.');
        }

        return DB::transaction(function () use ($source, $actor): Timetable {
            $copy = Timetable::create([
                'school_id' => $source->school_id,
                'academic_calendar_year_id' => $source->academic_calendar_year_id,
                'academic_calendar_term_id' => $source->academic_calendar_term_id,
                'name' => sprintf('%s (copie)', $source->name),
                'description' => $source->description,
                'status' => 'draft',
                'version' => 1,
                'total_entries' => 0,
                'created_by' => $actor?->getKey(),
            ]);

            $slotMap = [];

            foreach ($source->timeSlots()->get() as $slot) {
                $copySlot = TimeSlot::create([
                    'school_id' => $copy->school_id,
                    'timetable_id' => $copy->getKey(),
                    'teaching_period_id' => $slot->teaching_period_id,
                    'day_of_week' => $slot->day_of_week,
                    'period_number' => $slot->period_number,
                    'starts_at' => $slot->starts_at,
                    'ends_at' => $slot->ends_at,
                    'week_start' => $slot->week_start,
                    'week_end' => $slot->week_end,
                    'label' => $slot->label,
                ]);

                $slotMap[$slot->getKey()] = $copySlot->getKey();
            }

            $entryMap = [];

            foreach ($source->entries()->get() as $entry) {
                $copyEntry = ScheduleEntry::create([
                    'school_id' => $copy->school_id,
                    'timetable_id' => $copy->getKey(),
                    'time_slot_id' => $slotMap[$entry->time_slot_id] ?? null,
                    'academic_subject_id' => $entry->academic_subject_id,
                    'school_class_id' => $entry->school_class_id,
                    'class_section_id' => $entry->class_section_id,
                    'staff_id' => $entry->staff_id,
                    'room_id' => $entry->room_id,
                    'day_of_week' => $entry->day_of_week,
                    'period_number' => $entry->period_number,
                    'starts_at' => $entry->starts_at,
                    'ends_at' => $entry->ends_at,
                    'duration_minutes' => $entry->duration_minutes,
                    'entry_type' => $entry->entry_type?->value,
                    'status' => 'scheduled',
                    'is_recurring' => $entry->is_recurring,
                    'notes' => $entry->notes,
                ]);

                $entryMap[$entry->getKey()] = $copyEntry->getKey();
            }

            foreach ($source->recurringPatterns()->get() as $pattern) {
                RecurringPattern::create([
                    'school_id' => $copy->school_id,
                    'timetable_id' => $copy->getKey(),
                    'schedule_entry_id' => $entryMap[$pattern->schedule_entry_id] ?? null,
                    'time_slot_id' => $slotMap[$pattern->time_slot_id] ?? null,
                    'frequency' => $pattern->frequency?->value,
                    'interval' => $pattern->interval,
                    'occurs_on_days' => $pattern->occurs_on_days,
                    'starts_on' => $pattern->starts_on,
                    'ends_on' => $pattern->ends_on,
                    'end_after_occurrences' => $pattern->end_after_occurrences,
                    'exceptions' => $pattern->exceptions,
                ]);
            }

            foreach ($source->availabilityConstraints()->get() as $constraint) {
                AvailabilityConstraint::create([
                    'school_id' => $copy->school_id,
                    'timetable_id' => $copy->getKey(),
                    'time_slot_id' => $slotMap[$constraint->time_slot_id] ?? null,
                    'constraint_type' => $constraint->constraint_type?->value,
                    'target_type' => $constraint->target_type,
                    'target_id' => $constraint->target_id,
                    'day_of_week' => $constraint->day_of_week,
                    'starts_at' => $constraint->starts_at,
                    'ends_at' => $constraint->ends_at,
                    'starts_on' => $constraint->starts_on,
                    'ends_on' => $constraint->ends_on,
                    'reason' => $constraint->reason,
                ]);
            }

            $copy->update(['total_entries' => count($entryMap)]);

            TimetableDuplicated::dispatch($copy->refresh(), $source, $actor);

            return $copy->refresh();
        });
    }
}
