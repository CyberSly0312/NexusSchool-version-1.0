<?php

declare(strict_types=1);

namespace App\Domains\Timetable\Application\DTOs;

use App\Domains\Timetable\Domain\Enums\AvailabilityConstraintType;

/**
 * Data transfer object for blocking a resource inside a timetable (SPR-007).
 * The target is a generic (type, id) pair so no concrete model leaks in.
 */
final readonly class AvailabilityConstraintData
{
    public function __construct(
        public string $schoolId,
        public int|string $timetableId,
        public AvailabilityConstraintType $constraintType,
        public string $targetId,
        public int|string|null $timeSlotId = null,
        public ?int $dayOfWeek = null,
        public ?string $startsAt = null,
        public ?string $endsAt = null,
        public ?string $startsOn = null,
        public ?string $endsOn = null,
        public ?string $reason = null,
    ) {
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            schoolId: (string) $data['school_id'],
            timetableId: (int) $data['timetable_id'],
            constraintType: AvailabilityConstraintType::from((string) $data['constraint_type']),
            targetId: (string) $data['target_id'],
            timeSlotId: self::nullableIntOrString($data['time_slot_id'] ?? null),
            dayOfWeek: isset($data['day_of_week']) ? (int) $data['day_of_week'] : null,
            startsAt: self::nullableString($data['starts_at'] ?? null),
            endsAt: self::nullableString($data['ends_at'] ?? null),
            startsOn: self::nullableString($data['starts_on'] ?? null),
            endsOn: self::nullableString($data['ends_on'] ?? null),
            reason: self::nullableString($data['reason'] ?? null),
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
