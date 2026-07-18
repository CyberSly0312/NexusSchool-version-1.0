<?php

declare(strict_types=1);

namespace App\Domains\AcademicClass\DTOs;

readonly class AcademicClassData
{
    public function __construct(
        public int $academicYearId,
        public int $programId,
        public int $gradeLevelId,
        public int $sectionId,
        public string $name,
        public string $code,
        public ?string $description = null,
        public int $capacity = 0,
        public int $displayOrder = 0,
        public bool $isActive = true,
        public ?int $homeroomTeacherId = null,
        public ?string $classroom = null,
        public ?int $classroomId = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            academicYearId: $data['academic_year_id'],
            programId: $data['program_id'],
            gradeLevelId: $data['grade_level_id'],
            sectionId: $data['section_id'],
            name: $data['name'],
            code: $data['code'],
            description: $data['description'] ?? null,
            capacity: $data['capacity'] ?? 0,
            displayOrder: $data['display_order'] ?? 0,
            isActive: $data['is_active'] ?? true,
            homeroomTeacherId: $data['homeroom_teacher_id'] ?? null,
            classroom: $data['classroom'] ?? null,
            classroomId: $data['classroom_id'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'academic_year_id' => $this->academicYearId,
            'program_id' => $this->programId,
            'grade_level_id' => $this->gradeLevelId,
            'section_id' => $this->sectionId,
            'name' => $this->name,
            'code' => $this->code,
            'description' => $this->description,
            'capacity' => $this->capacity,
            'display_order' => $this->displayOrder,
            'status' => 'active',
            'is_active' => $this->isActive,
            'homeroom_teacher_id' => $this->homeroomTeacherId,
            'classroom' => $this->classroom,
            'classroom_id' => $this->classroomId,
        ];
    }
}
