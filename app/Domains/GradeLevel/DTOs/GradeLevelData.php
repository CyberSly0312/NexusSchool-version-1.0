<?php

namespace App\Domains\GradeLevel\DTOs;

readonly class GradeLevelData
{
    public function __construct(
        public int $programId,
        public string $name,
        public string $code,
        public int $levelOrder,
        public string $educationalCycle,
        public ?string $shortName = null,
        public ?string $description = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            programId: $data['program_id'],
            name: $data['name'],
            code: $data['code'],
            levelOrder: $data['level_order'],
            educationalCycle: $data['educational_cycle'],
            shortName: $data['short_name'] ?? null,
            description: $data['description'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'program_id' => $this->programId,
            'name' => $this->name,
            'code' => $this->code,
            'short_name' => $this->shortName,
            'level_order' => $this->levelOrder,
            'educational_cycle' => $this->educationalCycle,
            'description' => $this->description,
            'status' => 'active',
        ];
    }
}
