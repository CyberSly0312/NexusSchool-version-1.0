<?php

declare(strict_types=1);

namespace App\Domains\Timetable\Application\DTOs;

use App\Domains\Timetable\Domain\Enums\ConflictType;

/**
 * A single conflict entry produced by the conflict engine. Value-level DTO, free
 * of any model, so reports can be rendered without touching persistence.
 */
final readonly class ConflictReportData
{
    public function __construct(
        public ConflictType $type,
        public string $reason,
        public int|string $timetableId,
        public ?int $entryId = null,
        public ?int $conflictingEntryId = null,
        public ?string $targetType = null,
        public ?string $targetId = null,
        public ?int $dayOfWeek = null,
        public ?string $startsAt = null,
        public ?string $endsAt = null,
    ) {
    }

    public static function from(array $data): self
    {
        return new self(
            type: ConflictType::from((string) $data['conflict_type']),
            reason: (string) $data['reason'],
            timetableId: $data['timetable_id'],
            entryId: $data['entry_id'] ?? null,
            conflictingEntryId: $data['conflicting_entry_id'] ?? null,
            targetType: $data['target_type'] ?? null,
            targetId: $data['target_id'] ?? null,
            dayOfWeek: $data['day_of_week'] ?? null,
            startsAt: $data['starts_at'] ?? null,
            endsAt: $data['ends_at'] ?? null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'conflict_type' => $this->type->value,
            'reason' => $this->reason,
            'timetable_id' => $this->timetableId,
            'entry_id' => $this->entryId,
            'conflicting_entry_id' => $this->conflictingEntryId,
            'target_type' => $this->targetType,
            'target_id' => $this->targetId,
            'day_of_week' => $this->dayOfWeek,
            'starts_at' => $this->startsAt,
            'ends_at' => $this->endsAt,
        ];
    }
}
