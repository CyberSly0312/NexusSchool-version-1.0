<?php

namespace App\Domains\StudentEnrollment\DTOs;

readonly class StudentEnrollmentData
{
    public function __construct(
        public string $schoolId,
        public int $studentId,
        public int $academicYearId,
        public int $academicClassId,
        public int $programId,
        public int $gradeLevelId,
        public int $sectionId,
        public ?int $classroomId = null,
        public ?int $academicTermId = null,
        public ?string $enrollmentNumber = null,
        public ?string $enrollmentDate = null,
        public ?string $effectiveDate = null,
        public string $enrollmentType = 'new_admission',
        public string $status = 'pending',
        public ?string $previousSchool = null,
        public ?string $transferReason = null,
        public ?string $exitDate = null,
        public ?string $exitReason = null,
        public ?string $notes = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            schoolId: $data['school_id'],
            studentId: $data['student_id'],
            academicYearId: $data['academic_year_id'],
            academicClassId: $data['academic_class_id'],
            programId: $data['program_id'],
            gradeLevelId: $data['grade_level_id'],
            sectionId: $data['section_id'],
            classroomId: $data['classroom_id'] ?? null,
            academicTermId: $data['academic_term_id'] ?? null,
            enrollmentNumber: $data['enrollment_number'] ?? null,
            enrollmentDate: $data['enrollment_date'] ?? null,
            effectiveDate: $data['effective_date'] ?? null,
            enrollmentType: $data['enrollment_type'] ?? 'new_admission',
            status: $data['status'] ?? 'pending',
            previousSchool: $data['previous_school'] ?? null,
            transferReason: $data['transfer_reason'] ?? null,
            exitDate: $data['exit_date'] ?? null,
            exitReason: $data['exit_reason'] ?? null,
            notes: $data['notes'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'school_id' => $this->schoolId,
            'student_id' => $this->studentId,
            'academic_year_id' => $this->academicYearId,
            'academic_class_id' => $this->academicClassId,
            'program_id' => $this->programId,
            'grade_level_id' => $this->gradeLevelId,
            'section_id' => $this->sectionId,
            'classroom_id' => $this->classroomId,
            'academic_term_id' => $this->academicTermId,
            'enrollment_number' => $this->enrollmentNumber,
            'enrollment_date' => $this->enrollmentDate,
            'effective_date' => $this->effectiveDate,
            'enrollment_type' => $this->enrollmentType,
            'status' => $this->status,
            'previous_school' => $this->previousSchool,
            'transfer_reason' => $this->transferReason,
            'exit_date' => $this->exitDate,
            'exit_reason' => $this->exitReason,
            'notes' => $this->notes,
        ];
    }
}
