<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Application\DTOs;

/**
 * One row of a bulk attendance write (SPR-008).
 */
final readonly class BulkAttendanceItem
{
    public function __construct(
        public int|string $enrollmentId,
        public string $status,
        public ?string $checkInTime = null,
        public ?string $checkOutTime = null,
        public ?int $lateMinutes = null,
    ) {
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            enrollmentId: (int) $data['enrollment_id'],
            status: (string) $data['status'],
            checkInTime: self::nullableString($data['check_in_time'] ?? null),
            checkOutTime: self::nullableString($data['check_out_time'] ?? null),
            lateMinutes: isset($data['late_minutes']) ? (int) $data['late_minutes'] : null,
        );
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
