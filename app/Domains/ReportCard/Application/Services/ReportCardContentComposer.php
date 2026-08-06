<?php

declare(strict_types=1);

namespace App\Domains\ReportCard\Application\Services;

use App\Domains\ReportCard\Domain\Contracts\AcademicPoliciesContract;
use App\Domains\ReportCard\Domain\Contracts\AttendanceContract;
use App\Domains\ReportCard\Domain\Contracts\ClassCouncilContract;
use App\Domains\ReportCard\Domain\Contracts\GradebookContract;
use App\Domains\ReportCard\Domain\References\AcademicPeriodReference;
use App\Domains\ReportCard\Domain\References\AttendanceSummaryReference;
use App\Domains\ReportCard\Domain\References\CouncilDecisionReference;
use App\Domains\ReportCard\Domain\References\EnrollmentReference;
use App\Domains\ReportCard\Domain\References\GradebookEntryReference;
use App\Domains\ReportCard\Domain\References\GradebookStandingReference;
use App\Domains\ReportCard\Domain\References\PolicyReference;

/**
 * Composes the snapshot of one bulletin (SPR-011).
 *
 * The engine never performs any calculation: every figure is read verbatim from
 * the validated upstream engines through the domain contracts and serialized
 * into the entry content. The resulting snapshot is passed to the Academic
 * Communication engine, which owns the official document.
 */
final class ReportCardContentComposer
{
    public function __construct(
        private readonly GradebookContract $gradebook,
        private readonly AttendanceContract $attendance,
        private readonly AcademicPoliciesContract $policies,
        private readonly ClassCouncilContract $council,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function compose(
        string $schoolId,
        EnrollmentReference $enrollment,
        AcademicPeriodReference $period,
        int|string $yearId,
        int|string $termId,
    ): array {
        $student = $enrollment->student;

        $grades = $this->gradebook->studentGrades($schoolId, $student->id, $yearId, $termId);
        $standing = $this->gradebook->studentStanding($schoolId, $student->id, $yearId, $termId);
        $attendance = $this->attendance->summaryForStudent($schoolId, $student->id, $yearId, $termId);
        $policies = $this->policies->forPeriod($schoolId, $yearId, $termId);
        $council = $this->council->decisionsForStudent($schoolId, $student->id, $yearId, $termId);

        return [
            'student' => [
                'id' => $student->id,
                'full_name' => $student->fullName,
                'student_number' => $student->studentNumber,
            ],
            'period' => [
                'year_id' => $period->yearId,
                'year_name' => $period->yearName,
                'term_id' => $period->termId,
                'term_name' => $period->termName,
                'class_id' => $period->classId,
                'class_name' => $period->className,
                'section_id' => $period->sectionId,
                'section_name' => $period->sectionName,
            ],
            'gradebook' => array_map(
                fn (GradebookEntryReference $entry): array => [
                    'subject_id' => $entry->subjectId,
                    'subject_name' => $entry->subjectName,
                    'subject_code' => $entry->subjectCode,
                    'average' => $entry->average,
                    'percentage' => $entry->percentage,
                    'coefficient' => $entry->coefficient,
                    'grade_point' => $entry->gradePoint,
                    'letter_grade' => $entry->letterGrade,
                    'appreciation' => $entry->appreciation,
                    'rank' => $entry->rank,
                    'rank_denominator' => $entry->rankDenominator,
                ],
                $grades,
            ),
            'standing' => $standing !== null ? $this->serializeStanding($standing) : null,
            'attendance' => $attendance !== null ? $this->serializeAttendance($attendance) : null,
            'policies' => array_map(
                fn (PolicyReference $policy): array => [
                    'code' => $policy->code,
                    'label' => $policy->label,
                    'description' => $policy->description,
                ],
                $policies,
            ),
            'council' => array_map(
                fn (CouncilDecisionReference $decision): array => [
                    'code' => $decision->code,
                    'label' => $decision->label,
                    'decision' => $decision->decision,
                ],
                $council,
            ),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function serializeStanding(GradebookStandingReference $standing): array
    {
        return [
            'gpa' => $standing->gpa,
            'cgpa' => $standing->cgpa,
            'overall_percentage' => $standing->overallPercentage,
            'academic_standing' => $standing->academicStanding,
            'failed_subjects' => $standing->failedSubjects,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function serializeAttendance(AttendanceSummaryReference $attendance): array
    {
        return [
            'expected_days' => $attendance->expectedDays,
            'present_days' => $attendance->presentDays,
            'absent_days' => $attendance->absentDays,
            'late_days' => $attendance->lateDays,
            'rate' => $attendance->attendanceRate,
            'period_label' => $attendance->periodLabel,
        ];
    }
}
