<?php

declare(strict_types=1);

namespace App\Domains\Gradebook\Application\DTOs;

/**
 * Data required to create a term/subject gradebook (SPR-010).
 */
final readonly class CreateGradebookData
{
    public function __construct(
        public string $schoolId,
        public int|string $yearId,
        public int|string $termId,
        public int|string $subjectId,
        public int|string $classId,
        public int|string|null $sectionId = null,
        public int|string|null $staffId = null,
        public float $coefficient = 1.0,
        public ?string $notes = null,
    ) {
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            schoolId: (string) $data['school_id'],
            yearId: (int) $data['academic_calendar_year_id'],
            termId: (int) $data['academic_calendar_term_id'],
            subjectId: (int) $data['academic_subject_id'],
            classId: (int) $data['school_class_id'],
            sectionId: isset($data['class_section_id']) ? (int) $data['class_section_id'] : null,
            staffId: isset($data['staff_id']) ? (int) $data['staff_id'] : null,
            coefficient: isset($data['coefficient']) ? (float) $data['coefficient'] : 1.0,
            notes: isset($data['notes']) ? trim((string) $data['notes']) : null,
        );
    }
}
