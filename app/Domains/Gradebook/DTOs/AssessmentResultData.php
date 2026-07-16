<?php

namespace App\Domains\Gradebook\DTOs;

readonly class AssessmentResultData
{
    public function __construct(
        public string $schoolId,
        public int $studentId,
        public ?int $studentEnrollmentId,
        public int $assessmentId,
        public int $subjectId,
        public int $academicYearId,
        public int $academicTermId,
        public ?int $userId = null,
        public float $rawScore = 0,
        public ?float $adjustedScore = null,
        public float $maximumScore = 100,
        public ?float $percentage = null,
        public ?string $letterGrade = null,
        public ?float $gradePoint = null,
        public float $coefficient = 1.00,
        public ?float $weightedScore = null,
        public ?int $rankInAssessment = null,
        public bool $isAbsent = false,
        public bool $isExcused = false,
        public bool $isPublished = false,
        public bool $isLocked = false,
        public string $status = 'draft',
        public ?string $remarks = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            schoolId: $data['school_id'],
            studentId: $data['student_id'],
            studentEnrollmentId: $data['student_enrollment_id'] ?? null,
            assessmentId: $data['assessment_id'],
            subjectId: $data['subject_id'],
            academicYearId: $data['academic_year_id'],
            academicTermId: $data['academic_term_id'],
            userId: $data['user_id'] ?? null,
            rawScore: (float) ($data['raw_score'] ?? 0),
            adjustedScore: isset($data['adjusted_score']) ? (float) $data['adjusted_score'] : null,
            maximumScore: (float) ($data['maximum_score'] ?? 100),
            percentage: isset($data['percentage']) ? (float) $data['percentage'] : null,
            letterGrade: $data['letter_grade'] ?? null,
            gradePoint: isset($data['grade_point']) ? (float) $data['grade_point'] : null,
            coefficient: (float) ($data['coefficient'] ?? 1.00),
            weightedScore: isset($data['weighted_score']) ? (float) $data['weighted_score'] : null,
            rankInAssessment: isset($data['rank_in_assessment']) ? (int) $data['rank_in_assessment'] : null,
            isAbsent: (bool) ($data['is_absent'] ?? false),
            isExcused: (bool) ($data['is_excused'] ?? false),
            isPublished: (bool) ($data['is_published'] ?? false),
            isLocked: (bool) ($data['is_locked'] ?? false),
            status: $data['status'] ?? 'draft',
            remarks: $data['remarks'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'school_id' => $this->schoolId,
            'student_id' => $this->studentId,
            'student_enrollment_id' => $this->studentEnrollmentId,
            'assessment_id' => $this->assessmentId,
            'subject_id' => $this->subjectId,
            'academic_year_id' => $this->academicYearId,
            'academic_term_id' => $this->academicTermId,
            'user_id' => $this->userId,
            'raw_score' => $this->rawScore,
            'adjusted_score' => $this->adjustedScore,
            'maximum_score' => $this->maximumScore,
            'percentage' => $this->percentage,
            'letter_grade' => $this->letterGrade,
            'grade_point' => $this->gradePoint,
            'coefficient' => $this->coefficient,
            'weighted_score' => $this->weightedScore,
            'rank_in_assessment' => $this->rankInAssessment,
            'is_absent' => $this->isAbsent,
            'is_excused' => $this->isExcused,
            'is_published' => $this->isPublished,
            'is_locked' => $this->isLocked,
            'status' => $this->status,
            'remarks' => $this->remarks,
        ];
    }
}
