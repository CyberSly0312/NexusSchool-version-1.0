<?php

declare(strict_types=1);

namespace App\Domains\Program\DTOs;

readonly class ProgramData
{
    public function __construct(
        public string $schoolId,
        public string $name,
        public string $code,
        public string $educationalLevel,
        public string $duration,
        public ?string $description = null,
        public ?int $departmentId = null,
        public int $order = 0,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            schoolId: $data['school_id'],
            name: $data['name'],
            code: $data['code'],
            educationalLevel: $data['educational_level'],
            duration: $data['duration'],
            description: $data['description'] ?? null,
            departmentId: $data['department_id'] ?? null,
            order: $data['order'] ?? 0,
        );
    }

    public function toArray(): array
    {
        return [
            'school_id' => $this->schoolId,
            'department_id' => $this->departmentId,
            'name' => $this->name,
            'code' => $this->code,
            'description' => $this->description,
            'educational_level' => $this->educationalLevel,
            'duration' => $this->duration,
            'order' => $this->order,
            'status' => 'active',
        ];
    }
}
