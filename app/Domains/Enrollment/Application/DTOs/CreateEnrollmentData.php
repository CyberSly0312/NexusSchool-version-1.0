<?php

declare(strict_types=1);

namespace App\Domains\Enrollment\Application\DTOs;

use App\Domains\Enrollment\Domain\Enums\EnrollmentStatus;
use App\Domains\Enrollment\Domain\Enums\EnrollmentType;

final readonly class CreateEnrollmentData
{
    public function __construct(
        public string $schoolId,
        public int $studentId,
        public int $academicCalendarYearId,
        public int $academicProgramId,
        public int $educationLevelId,
        public int $schoolClassId,
        public ?int $classSectionId = null,
        public EnrollmentType $enrollmentType = EnrollmentType::New,
        public ?string $enrolledAt = null,
        public ?string $enrollmentNumber = null,
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
            studentId: (int) $data['student_id'],
            academicCalendarYearId: (int) $data['academic_calendar_year_id'],
            academicProgramId: (int) $data['academic_program_id'],
            educationLevelId: (int) $data['education_level_id'],
            schoolClassId: (int) $data['school_class_id'],
            classSectionId: isset($data['class_section_id']) ? (int) $data['class_section_id'] : null,
            enrollmentType: isset($data['enrollment_type'])
                ? EnrollmentType::from((string) $data['enrollment_type'])
                : EnrollmentType::New,
            enrolledAt: $data['enrolled_at'] ?? null,
            enrollmentNumber: $data['enrollment_number'] ?? null,
            notes: $data['notes'] ?? null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'school_id' => $this->schoolId,
            'student_id' => $this->studentId,
            'academic_calendar_year_id' => $this->academicCalendarYearId,
            'academic_program_id' => $this->academicProgramId,
            'education_level_id' => $this->educationLevelId,
            'school_class_id' => $this->schoolClassId,
            'class_section_id' => $this->classSectionId,
            'enrollment_number' => $this->enrollmentNumber,
            'enrollment_type' => $this->enrollmentType->value,
            'status' => EnrollmentStatus::Active->value,
            'enrolled_at' => $this->enrolledAt,
            'notes' => $this->notes,
        ];
    }
}
