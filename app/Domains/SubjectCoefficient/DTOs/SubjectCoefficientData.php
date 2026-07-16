<?php

namespace App\Domains\SubjectCoefficient\DTOs;

readonly class SubjectCoefficientData
{
    public function __construct(
        public string $schoolId,
        public int $subjectId,
        public int $programId,
        public int $gradeLevelId,
        public ?int $academicYearId,
        public string $coefficient,
        public string $effectiveFrom,
        public ?string $effectiveTo,
        public ?string $notes,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            schoolId: $data['school_id'],
            subjectId: $data['subject_id'],
            programId: $data['program_id'],
            gradeLevelId: $data['grade_level_id'],
            academicYearId: $data['academic_year_id'] ?? null,
            coefficient: (string) ($data['coefficient'] ?? '1.00'),
            effectiveFrom: $data['effective_from'],
            effectiveTo: $data['effective_to'] ?? null,
            notes: $data['notes'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'school_id' => $this->schoolId,
            'subject_id' => $this->subjectId,
            'program_id' => $this->programId,
            'grade_level_id' => $this->gradeLevelId,
            'academic_year_id' => $this->academicYearId,
            'coefficient' => $this->coefficient,
            'effective_from' => $this->effectiveFrom,
            'effective_to' => $this->effectiveTo,
            'notes' => $this->notes,
            'status' => 'active',
        ];
    }
}
