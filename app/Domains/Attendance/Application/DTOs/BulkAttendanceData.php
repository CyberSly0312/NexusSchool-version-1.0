<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Application\DTOs;

/**
 * Data transfer object for a bulk attendance write on one session (SPR-008).
 *
 * @param  list<BulkAttendanceItem>  $items
 */
final readonly class BulkAttendanceData
{
    public function __construct(
        public int|string $sessionId,
        public array $items,
    ) {
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        $items = array_map(
            fn (array $item): BulkAttendanceItem => BulkAttendanceItem::fromArray($item),
            (array) ($data['items'] ?? []),
        );

        return new self(
            sessionId: (int) $data['attendance_session_id'],
            items: $items,
        );
    }
}
