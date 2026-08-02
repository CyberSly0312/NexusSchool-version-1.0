<?php

declare(strict_types=1);

namespace App\Domains\Academic\Application\DTOs;

use App\Domains\Academic\Domain\ValueObjects\Coefficient;

final readonly class CoefficientData
{
    public function __construct(
        public string $schoolId,
        public int $schoolClassId,
        public int $subjectId,
        public float $coefficient,
        public bool $isOptional = false,
        public ?string $description = null,
    ) {
        new Coefficient($this->coefficient, $this->isOptional);
    }

    public static function fromArray(array $data): self
    {
        return new self(
            schoolId: $data['school_id'],
            schoolClassId: (int) $data['school_class_id'],
            subjectId: (int) $data['academic_subject_id'],
            coefficient: (float) $data['coefficient'],
            isOptional: (bool) ($data['is_optional'] ?? false),
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
            'school_class_id' => $this->schoolClassId,
            'academic_subject_id' => $this->subjectId,
            'coefficient' => $this->coefficient,
            'is_optional' => $this->isOptional,
            'description' => $this->description,
        ];
    }
}
