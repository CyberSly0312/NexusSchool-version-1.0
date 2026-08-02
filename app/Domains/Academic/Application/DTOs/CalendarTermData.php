<?php

declare(strict_types=1);

namespace App\Domains\Academic\Application\DTOs;

use App\Domains\Academic\Domain\Enums\CalendarTermStatus;

final readonly class CalendarTermData
{
    public function __construct(
        public string $schoolId,
        public int $calendarYearId,
        public string $name,
        public string $startsOn,
        public string $endsOn,
        public ?string $code = null,
        public CalendarTermStatus $status = CalendarTermStatus::Planned,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            schoolId: $data['school_id'],
            calendarYearId: (int) $data['calendar_year_id'],
            name: $data['name'],
            startsOn: $data['starts_on'],
            endsOn: $data['ends_on'],
            code: $data['code'] ?? null,
            status: isset($data['status'])
                ? CalendarTermStatus::from($data['status'])
                : CalendarTermStatus::Planned,
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
            'name' => $this->name,
            'code' => $this->code,
            'starts_on' => $this->startsOn,
            'ends_on' => $this->endsOn,
            'status' => $this->status->value,
        ];
    }
}
