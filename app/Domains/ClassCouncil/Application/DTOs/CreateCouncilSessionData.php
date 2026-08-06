<?php

declare(strict_types=1);

namespace App\Domains\ClassCouncil\Application\DTOs;

/**
 * Data required to create a class council session (SPR-013).
 */
final readonly class CreateCouncilSessionData
{
    public function __construct(
        public string $schoolId,
        public int|string $yearId,
        public int|string $termId,
        public int|string $classId,
        public int|string|null $sectionId = null,
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
            classId: (int) $data['school_class_id'],
            sectionId: isset($data['class_section_id']) ? (int) $data['class_section_id'] : null,
        );
    }
}
