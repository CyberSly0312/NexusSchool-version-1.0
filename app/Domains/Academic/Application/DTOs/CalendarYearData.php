<?php

declare(strict_types=1);

namespace App\Domains\Academic\Application\DTOs;

use App\Domains\Academic\Domain\Enums\CalendarYearStatus;

final readonly class CalendarYearData
{
    public function __construct(
        public string $schoolId,
        public string $name,
        public string $startDate,
        public string $endDate,
        public ?string $code = null,
        public ?string $description = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            schoolId: $data['school_id'],
            name: $data['name'],
            startDate: $data['start_date'],
            endDate: $data['end_date'],
            code: $data['code'] ?? null,
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
            'name' => $this->name,
            'code' => $this->code,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'description' => $this->description,
            'status' => CalendarYearStatus::Draft->value,
        ];
    }
}
