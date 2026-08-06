<?php

declare(strict_types=1);

namespace App\Domains\Timetable\Application\DTOs;

/**
 * Data transfer object for publishing a Timetable (SPR-007).
 */
final readonly class PublishTimetableData
{
    public function __construct(
        public int|string $timetableId,
        public ?string $reason = null,
    ) {
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            timetableId: (int) $data['timetable_id'],
            reason: isset($data['reason']) && $data['reason'] !== '' ? (string) $data['reason'] : null,
        );
    }
}
