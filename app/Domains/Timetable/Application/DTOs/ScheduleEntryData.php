<?php

declare(strict_types=1);

namespace App\Domains\Timetable\Application\DTOs;

use App\Domains\Timetable\Domain\Enums\ScheduleEntryType;

/**
 * Data transfer object for a single schedule entry (SPR-007).
 */
final readonly class ScheduleEntryData
{
    public function __construct(
        public string $schoolId,
        public int|string $timetableId,
        public int|string $academicSubjectId,
        public int|string $schoolClassId,
        public int|string|null $classSectionId = null,
        public int|string|null $staffId = null,
        public string|null $roomId = null,
        public int $dayOfWeek = 0,
        public int $periodNumber = 1,
        public string $startsAt = '08:00',
        public string $endsAt = '08:55',
        public ?int $durationMinutes = null,
        public ScheduleEntryType $entryType = ScheduleEntryType::Lesson,
        public bool $isRecurring = false,
        public int|string|null $timeSlotId = null,
        public ?string $notes = null,
    ) {
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            schoolId: (string) $data['school_id'],
            timetableId: self::toIntOrString($data['timetable_id']),
            academicSubjectId: self::toIntOrString($data['academic_subject_id']),
            schoolClassId: self::toIntOrString($data['school_class_id']),
            classSectionId: self::nullableIntOrString($data['class_section_id'] ?? null),
            staffId: self::nullableIntOrString($data['staff_id'] ?? null),
            roomId: self::nullableString($data['room_id'] ?? null),
            dayOfWeek: (int) ($data['day_of_week'] ?? 0),
            periodNumber: (int) ($data['period_number'] ?? 1),
            startsAt: (string) ($data['starts_at'] ?? '08:00'),
            endsAt: (string) ($data['ends_at'] ?? '08:55'),
            durationMinutes: isset($data['duration_minutes']) ? (int) $data['duration_minutes'] : null,
            entryType: isset($data['entry_type'])
                ? ScheduleEntryType::from((string) $data['entry_type'])
                : ScheduleEntryType::Lesson,
            isRecurring: (bool) ($data['is_recurring'] ?? false),
            timeSlotId: self::nullableIntOrString($data['time_slot_id'] ?? null),
            notes: self::nullableString($data['notes'] ?? null),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'school_id' => $this->schoolId,
            'timetable_id' => $this->timetableId,
            'academic_subject_id' => $this->academicSubjectId,
            'school_class_id' => $this->schoolClassId,
            'class_section_id' => $this->classSectionId,
            'staff_id' => $this->staffId,
            'room_id' => $this->roomId,
            'day_of_week' => $this->dayOfWeek,
            'period_number' => $this->periodNumber,
            'starts_at' => $this->startsAt,
            'ends_at' => $this->endsAt,
            'duration_minutes' => $this->durationMinutes ?? $this->inferredDurationMinutes(),
            'entry_type' => $this->entryType->value,
            'is_recurring' => $this->isRecurring,
            'time_slot_id' => $this->timeSlotId,
            'notes' => $this->notes,
        ];
    }

    public function inferredDurationMinutes(): int
    {
        $start = strtotime($this->startsAt);
        $end = strtotime($this->endsAt);

        return (int) round(($end - $start) / 60);
    }

    private static function toIntOrString(mixed $value): int|string
    {
        return is_numeric($value) ? (int) $value : (string) $value;
    }

    private static function nullableIntOrString(mixed $value): int|string|null
    {
        if ($value === null || $value === '') {
            return null;
        }

        return self::toIntOrString($value);
    }

    private static function nullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $trimmed = trim((string) $value);

        return $trimmed === '' ? null : $trimmed;
    }
}
