<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Application\DTOs;

use Carbon\CarbonImmutable;

/**
 * Data transfer object for creating an assessment (SPR-009).
 */
final readonly class CreateAssessmentData
{
    public function __construct(
        public string $schoolId,
        public int|string $yearId,
        public int|string $termId,
        public int|string $subjectId,
        public int|string $classId,
        public int|string $assessmentTypeId,
        public string $title,
        public float $maximumScore,
        public int|string|null $sectionId = null,
        public int|string|null $staffId = null,
        public ?float $passingScore = null,
        public ?float $weight = null,
        public ?CarbonImmutable $scheduledAt = null,
        public ?string $description = null,
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
            assessmentTypeId: (int) $data['assessment_type_id'],
            title: trim((string) $data['title']),
            maximumScore: (float) $data['maximum_score'],
            sectionId: isset($data['class_section_id']) ? (int) $data['class_section_id'] : null,
            staffId: isset($data['staff_id']) ? (int) $data['staff_id'] : null,
            passingScore: isset($data['passing_score']) ? (float) $data['passing_score'] : null,
            weight: isset($data['weight']) ? (float) $data['weight'] : null,
            scheduledAt: self::nullableDateTime($data['scheduled_at'] ?? null),
            description: self::nullableString($data['description'] ?? null),
            notes: self::nullableString($data['notes'] ?? null),
        );
    }

    private static function nullableDateTime(mixed $value): ?CarbonImmutable
    {
        if ($value === null || $value === '') {
            return null;
        }

        return CarbonImmutable::parse($value);
    }

    private static function nullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $trimmed = trim((string) $value);

        return $trimmed === '' ? null : $trimmed;
    }
}
