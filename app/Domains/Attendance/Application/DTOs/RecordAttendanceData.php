<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Application\DTOs;

use App\Domains\Attendance\Domain\Enums\AttendanceStatus;
use Carbon\CarbonImmutable;

/**
 * Data transfer object for recording a single student's attendance (SPR-008).
 */
final readonly class RecordAttendanceData
{
    public function __construct(
        public int|string $sessionId,
        public int|string $enrollmentId,
        public AttendanceStatus $status,
        public ?CarbonImmutable $checkInTime = null,
        public ?CarbonImmutable $checkOutTime = null,
        public ?int $lateMinutes = null,
        public ?string $notes = null,
    ) {
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            sessionId: (int) $data['attendance_session_id'],
            enrollmentId: (int) $data['enrollment_id'],
            status: AttendanceStatus::from((string) $data['status']),
            checkInTime: self::nullableDateTime($data['check_in_time'] ?? null),
            checkOutTime: self::nullableDateTime($data['check_out_time'] ?? null),
            lateMinutes: isset($data['late_minutes']) ? (int) $data['late_minutes'] : null,
            notes: self::nullableString($data['notes'] ?? null),
        );
    }

    private static function nullableDateTime(mixed $value): ?CarbonImmutable
    {
        if ($value === null || $value === '') {
            return null;
        }

        return CarbonImmutable::parse($value);
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
