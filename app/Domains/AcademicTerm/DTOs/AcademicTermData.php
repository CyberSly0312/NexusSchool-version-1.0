<?php

namespace App\Domains\AcademicTerm\DTOs;

readonly class AcademicTermData
{
    public function __construct(
        public int $academicYearId,
        public string $schoolId,
        public string $name,
        public string $startDate,
        public string $endDate,
        public int $order = 0,
        public ?string $code = null,
        public ?string $description = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            academicYearId: $data['academic_year_id'],
            schoolId: $data['school_id'],
            name: $data['name'],
            startDate: $data['start_date'],
            endDate: $data['end_date'],
            order: $data['order'] ?? 0,
            code: $data['code'] ?? null,
            description: $data['description'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'academic_year_id' => $this->academicYearId,
            'school_id' => $this->schoolId,
            'name' => $this->name,
            'code' => $this->code,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'order' => $this->order,
            'description' => $this->description,
            'status' => 'draft',
        ];
    }
}
