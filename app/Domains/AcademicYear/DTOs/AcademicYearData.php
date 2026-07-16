<?php

namespace App\Domains\AcademicYear\DTOs;

readonly class AcademicYearData
{
    public function __construct(
        public string|int $schoolId,
        public string $name,
        public string $startDate,
        public string $endDate,
        public ?string $code = null,
        public ?string $description = null,
    ) {}

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

    public function toArray(): array
    {
        return [
            'school_id' => $this->schoolId,
            'name' => $this->name,
            'code' => $this->code,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'description' => $this->description,
            'status' => 'draft',
        ];
    }
}
