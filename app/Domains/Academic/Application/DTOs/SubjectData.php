<?php

declare(strict_types=1);

namespace App\Domains\Academic\Application\DTOs;

use App\Domains\Academic\Domain\Enums\EntityStatus;

final readonly class SubjectData
{
    public function __construct(
        public string $schoolId,
        public string $name,
        public ?string $code = null,
        public ?string $abbreviation = null,
        public ?int $subjectGroupId = null,
        public ?string $description = null,
        public EntityStatus $status = EntityStatus::Active,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            schoolId: $data['school_id'],
            name: $data['name'],
            code: $data['code'] ?? null,
            abbreviation: $data['abbreviation'] ?? null,
            subjectGroupId: isset($data['subject_group_id']) ? (int) $data['subject_group_id'] : null,
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
            'subject_group_id' => $this->subjectGroupId,
            'name' => $this->name,
            'code' => $this->code,
            'abbreviation' => $this->abbreviation,
            'description' => $this->description,
            'status' => $this->status->value,
        ];
    }
}
