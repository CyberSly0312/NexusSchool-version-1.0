<?php

namespace App\Domains\Subject\DTOs;

readonly class SubjectData
{
    public function __construct(
        public string $schoolId,
        public int $subjectCategoryId,
        public string $name,
        public string $code,
        public ?string $shortName = null,
        public ?string $description = null,
        public int $displayOrder = 0,
        public ?string $color = null,
        public bool $isElective = false,
        public bool $isCoreSubject = false,
        public int $weeklyHours = 1,
        public ?int $departmentId = null,
        public ?int $programId = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            schoolId: $data['school_id'],
            subjectCategoryId: $data['subject_category_id'],
            name: $data['name'],
            code: $data['code'],
            shortName: $data['short_name'] ?? null,
            description: $data['description'] ?? null,
            displayOrder: $data['display_order'] ?? 0,
            color: $data['color'] ?? null,
            isElective: $data['is_elective'] ?? false,
            isCoreSubject: $data['is_core_subject'] ?? false,
            weeklyHours: $data['weekly_hours'] ?? 1,
            departmentId: $data['department_id'] ?? null,
            programId: $data['program_id'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'school_id' => $this->schoolId,
            'subject_category_id' => $this->subjectCategoryId,
            'name' => $this->name,
            'code' => $this->code,
            'short_name' => $this->shortName,
            'description' => $this->description,
            'display_order' => $this->displayOrder,
            'color' => $this->color,
            'is_elective' => $this->isElective,
            'is_core_subject' => $this->isCoreSubject,
            'weekly_hours' => $this->weeklyHours,
            'department_id' => $this->departmentId,
            'program_id' => $this->programId,
            'status' => 'active',
        ];
    }
}
