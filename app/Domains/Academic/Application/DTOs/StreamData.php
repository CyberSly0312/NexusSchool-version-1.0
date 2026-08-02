<?php

declare(strict_types=1);

namespace App\Domains\Academic\Application\DTOs;

use App\Domains\Academic\Domain\Enums\EntityStatus;

final readonly class StreamData
{
    public function __construct(
        public string $schoolId,
        public int $programId,
        public string $name,
        public ?int $educationLevelId = null,
        public ?string $code = null,
        public ?string $description = null,
        public EntityStatus $status = EntityStatus::Active,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            schoolId: $data['school_id'],
            programId: (int) $data['academic_program_id'],
            name: $data['name'],
            educationLevelId: isset($data['education_level_id']) ? (int) $data['education_level_id'] : null,
            code: $data['code'] ?? null,
            description: $data['description'] ?? null,
            status: isset($data['status']) ? EntityStatus::from($data['status']) : EntityStatus::Active,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'school_id' => $this->schoolId,
            'academic_program_id' => $this->programId,
            'education_level_id' => $this->educationLevelId,
            'name' => $this->name,
            'code' => $this->code,
            'description' => $this->description,
            'status' => $this->status->value,
        ];
    }
}
