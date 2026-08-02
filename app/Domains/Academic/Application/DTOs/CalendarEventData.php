<?php

declare(strict_types=1);

namespace App\Domains\Academic\Application\DTOs;

use App\Domains\Academic\Domain\Enums\CalendarEventType;

final readonly class CalendarEventData
{
    public function __construct(
        public string $schoolId,
        public int $calendarYearId,
        public string $title,
        public string $startsOn,
        public CalendarEventType $type = CalendarEventType::Event,
        public ?int $calendarTermId = null,
        public ?string $endsOn = null,
        public bool $isAllDay = true,
        public ?string $description = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            schoolId: $data['school_id'],
            calendarYearId: (int) $data['calendar_year_id'],
            title: $data['title'],
            startsOn: $data['starts_on'],
            type: isset($data['type']) ? CalendarEventType::from($data['type']) : CalendarEventType::Event,
            calendarTermId: isset($data['calendar_term_id']) ? (int) $data['calendar_term_id'] : null,
            endsOn: $data['ends_on'] ?? null,
            isAllDay: (bool) ($data['is_all_day'] ?? true),
            description: $data['description'] ?? null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'school_id' => $this->schoolId,
            'calendar_year_id' => $this->calendarYearId,
            'calendar_term_id' => $this->calendarTermId,
            'title' => $this->title,
            'type' => $this->type->value,
            'starts_on' => $this->startsOn,
            'ends_on' => $this->endsOn,
            'is_all_day' => $this->isAllDay,
            'description' => $this->description,
        ];
    }
}
