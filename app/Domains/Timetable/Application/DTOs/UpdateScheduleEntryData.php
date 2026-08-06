<?php

declare(strict_types=1);

namespace App\Domains\Timetable\Application\DTOs;

use App\Domains\Timetable\Domain\Enums\ScheduleEntryStatus;
use App\Domains\Timetable\Domain\Enums\ScheduleEntryType;

/**
 * Data transfer object for updating an existing schedule entry (SPR-007).
 */
final readonly class UpdateScheduleEntryData
{
    public function __construct(
        public int|string $entryId,
        public int|string|null $academicSubjectId = null,
        public int|string|null $schoolClassId = null,
        public int|string|null $classSectionId = null,
        public int|string|null $staffId = null,
        public string|null $roomId = null,
        public ?int $dayOfWeek = null,
        public ?int $periodNumber = null,
        public ?string $startsAt = null,
        public ?string $endsAt = null,
        public ?int $durationMinutes = null,
        public ?ScheduleEntryType $entryType = null,
        public ?ScheduleEntryStatus $status = null,
        public ?string $notes = null,
    ) {
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            entryId: (int) $data['entry_id'],
            academicSubjectId: self::nullableIntOrString($data['academic_subject_id'] ?? null),
            schoolClassId: self::nullableIntOrString($data['school_class_id'] ?? null),
            classSectionId: self::nullableIntOrString($data['class_section_id'] ?? null),
            staffId: self::nullableIntOrString($data['staff_id'] ?? null),
            roomId: self::nullableString($data['room_id'] ?? null),
            dayOfWeek: isset($data['day_of_week']) ? (int) $data['day_of_week'] : null,
            periodNumber: isset($data['period_number']) ? (int) $data['period_number'] : null,
            startsAt: self::nullableString($data['starts_at'] ?? null),
            endsAt: self::nullableString($data['ends_at'] ?? null),
            durationMinutes: isset($data['duration_minutes']) ? (int) $data['duration_minutes'] : null,
            entryType: isset($data['entry_type']) ? ScheduleEntryType::from((string) $data['entry_type']) : null,
            status: isset($data['status']) ? ScheduleEntryStatus::from((string) $data['status']) : null,
            notes: self::nullableString($data['notes'] ?? null),
        );
    }

    private static function nullableIntOrString(mixed $value): int|string|null
    {
        if ($value === null || $value === '') {
            return null;
        }

        return is_numeric($value) ? (int) $value : (string) $value;
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
