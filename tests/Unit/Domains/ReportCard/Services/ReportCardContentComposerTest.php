<?php

declare(strict_types=1);

use App\Domains\ReportCard\Application\Services\ReportCardContentComposer;
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
use App\Domains\ReportCard\Domain\References\StudentReference;

/**
 * Contract fakes that return fixed, non-computed figures. The composer must
 * serialize them verbatim and never perform any calculation.
 */
final class FakeGradebookContract implements GradebookContract
{
    public function studentGrades(string $schoolId, int|string $studentId, int|string $yearId, int|string $termId): array
    {
        return [
            new GradebookEntryReference(
                subjectId: 11,
                subjectName: 'Mathématiques',
                subjectCode: 'MATH',
                average: 15.75,
                percentage: 78.75,
                coefficient: 2.0,
                gradePoint: 3.5,
                letterGrade: 'TB',
                appreciation: 'Très bien',
                rank: 2,
                rankDenominator: 24,
            ),
        ];
    }

    public function studentStanding(string $schoolId, int|string $studentId, int|string $yearId, int|string $termId): ?GradebookStandingReference
    {
        return new GradebookStandingReference(
            gpa: 3.5,
            cgpa: 3.2,
            overallPercentage: 78.75,
            academicStanding: 'pass',
            failedSubjects: 0,
        );
    }
}

final class FakeAttendanceContract implements AttendanceContract
{
    public function summaryForStudent(string $schoolId, int|string $studentId, int|string $yearId, int|string $termId): ?AttendanceSummaryReference
    {
        return new AttendanceSummaryReference(
            expectedDays: 60,
            presentDays: 57,
            absentDays: 2,
            lateDays: 1,
            attendanceRate: 95.0,
            periodLabel: '2026-09-07 au 2026-12-18',
        );
    }
}

final class FakePoliciesContract implements AcademicPoliciesContract
{
    public function forPeriod(string $schoolId, int|string $yearId, int|string $termId): array
    {
        return [
            new PolicyReference(
                code: 'RT-01',
                label: 'Règlement des évaluations',
                description: 'Toute absence à une évaluation est justifiée par un certificat.',
            ),
        ];
    }
}

final class FakeCouncilContract implements ClassCouncilContract
{
    public function decisionsForStudent(string $schoolId, int|string $studentId, int|string $yearId, int|string $termId): array
    {
        return [
            new CouncilDecisionReference(
                code: 'ENC-01',
                label: 'Encouragements',
                decision: 'Décision du conseil de classe du 12/12/2026.',
            ),
        ];
    }
}

beforeEach(function (): void {
    $this->composer = new ReportCardContentComposer(
        gradebook: new FakeGradebookContract(),
        attendance: new FakeAttendanceContract(),
        policies: new FakePoliciesContract(),
        council: new FakeCouncilContract(),
    );

    $this->enrollment = new EnrollmentReference(
        id: 42,
        student: new StudentReference(
            id: 7,
            fullName: 'Jean Dupont',
            studentNumber: 'STU-2026-0001',
        ),
    );

    $this->period = new AcademicPeriodReference(
        classId: 3,
        className: '4e A',
        sectionId: null,
        sectionName: null,
        termId: 2,
        termName: '1er trimestre',
        yearId: 1,
        yearName: '2026-2027',
    );
});

test('the composer serializes every upstream figure verbatim', function (): void {
    $content = $this->composer->compose('school-1', $this->enrollment, $this->period, 1, 2);

    expect($content['student'])
        ->toMatchArray([
            'id' => 7,
            'full_name' => 'Jean Dupont',
            'student_number' => 'STU-2026-0001',
        ])
        ->and($content['period'])
        ->toMatchArray([
            'year_id' => 1,
            'year_name' => '2026-2027',
            'term_id' => 2,
            'term_name' => '1er trimestre',
            'class_id' => 3,
            'class_name' => '4e A',
            'section_id' => null,
            'section_name' => null,
        ])
        ->and($content['gradebook'][0])
        ->toMatchArray([
            'subject_id' => 11,
            'subject_name' => 'Mathématiques',
            'subject_code' => 'MATH',
            'average' => 15.75,
            'percentage' => 78.75,
            'coefficient' => 2.0,
            'grade_point' => 3.5,
            'letter_grade' => 'TB',
            'appreciation' => 'Très bien',
            'rank' => 2,
            'rank_denominator' => 24,
        ])
        ->and($content['standing'])
        ->toMatchArray([
            'gpa' => 3.5,
            'cgpa' => 3.2,
            'overall_percentage' => 78.75,
            'academic_standing' => 'pass',
            'failed_subjects' => 0,
        ])
        ->and($content['attendance'])
        ->toMatchArray([
            'expected_days' => 60,
            'present_days' => 57,
            'absent_days' => 2,
            'late_days' => 1,
            'rate' => 95.0,
            'period_label' => '2026-09-07 au 2026-12-18',
        ])
        ->and($content['policies'][0])
        ->toMatchArray([
            'code' => 'RT-01',
            'label' => 'Règlement des évaluations',
            'description' => 'Toute absence à une évaluation est justifiée par un certificat.',
        ])
        ->and($content['council'][0])
        ->toMatchArray([
            'code' => 'ENC-01',
            'label' => 'Encouragements',
            'decision' => 'Décision du conseil de classe du 12/12/2026.',
        ]);
});

test('the composer tolerates a student with no standing or attendance', function (): void {
    $composer = new ReportCardContentComposer(
        gradebook: new class () implements GradebookContract {
            public function studentGrades(string $schoolId, int|string $studentId, int|string $yearId, int|string $termId): array
            {
                return [];
            }

            public function studentStanding(string $schoolId, int|string $studentId, int|string $yearId, int|string $termId): ?GradebookStandingReference
            {
                return null;
            }
        },
        attendance: new class () implements AttendanceContract {
            public function summaryForStudent(string $schoolId, int|string $studentId, int|string $yearId, int|string $termId): ?AttendanceSummaryReference
            {
                return null;
            }
        },
        policies: new FakePoliciesContract(),
        council: new FakeCouncilContract(),
    );

    $content = $composer->compose('school-1', $this->enrollment, $this->period, 1, 2);

    expect($content['gradebook'])->toBe([])
        ->and($content['standing'])->toBeNull()
        ->and($content['attendance'])->toBeNull();
});
