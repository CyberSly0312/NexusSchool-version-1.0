<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Application\Actions;

use App\Domains\AcademicCommunication\Application\DTOs\GenerateDocumentData;
use App\Domains\AcademicCommunication\Application\Services\DocumentContentComposer;
use App\Domains\AcademicCommunication\Domain\Contracts\AssessmentContract;
use App\Domains\AcademicCommunication\Domain\Contracts\AttendanceContract;
use App\Domains\AcademicCommunication\Domain\Contracts\GradebookContract;
use App\Domains\AcademicCommunication\Domain\Contracts\StudentIdentityContract;
use App\Domains\AcademicCommunication\Domain\Enums\DocumentOrigin;
use App\Domains\AcademicCommunication\Domain\Exceptions\MissingDocumentContextException;
use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocument;
use App\Models\User;

/**
 * Generates the official report card (bulletin) of a student (SPR-012).
 *
 * The content is composed from validated Assessment and Gradebook aggregates
 * plus the attendance summary — business engines supply the data, this engine
 * renders and owns the document. The report card is student-scoped: its
 * sections (results, gradebook, attendance) are delivered to the student and
 * their guardians through the visibility policy, never to other families.
 */
final class GenerateReportCardAction
{
    public function __construct(
        private readonly GenerateDocumentAction $generateDocument,
        private readonly DocumentContentComposer $composer,
        private readonly StudentIdentityContract $studentIdentity,
        private readonly AssessmentContract $assessment,
        private readonly GradebookContract $gradebook,
        private readonly AttendanceContract $attendance,
    ) {
    }

    public function execute(GenerateDocumentData $data, ?User $actor = null): AcademicDocument
    {
        if ($data->studentId === null) {
            throw MissingDocumentContextException::missing('student');
        }

        if ($data->yearId === null || $data->termId === null) {
            throw MissingDocumentContextException::missing('academic period');
        }

        $student = $this->studentIdentity->student($data->schoolId, $data->studentId);

        if ($student === null) {
            throw MissingDocumentContextException::missing('student');
        }

        $results = $this->assessment->studentAssessmentSummaries(
            $data->schoolId,
            $data->studentId,
            $data->yearId,
            $data->termId,
        );

        $gradebook = $this->gradebook->studentGrades(
            $data->schoolId,
            $data->studentId,
            $data->yearId,
            $data->termId,
        );

        $attendance = $this->attendance->summaryForStudent(
            $data->schoolId,
            $data->studentId,
            $data->yearId,
            $data->termId,
        );

        $content = array_merge(
            $this->composer->schoolBlock($data->schoolId),
            $this->composer->frameBlock(
                $data->schoolId,
                $data->yearId,
                $data->termId,
                $data->classId,
                $data->sectionId,
            ),
            [
                'student' => [
                    'id' => $student->id,
                    'full_name' => $student->fullName,
                    'student_number' => $student->studentNumber,
                    'class' => $student->class,
                    'section' => $student->section,
                ],
                'results' => $this->serializeResults($results),
                'gradebook' => $this->serializeGradebook($gradebook),
                'attendance' => $attendance !== null
                    ? $this->serializeAttendance($attendance)
                    : null,
            ],
        );

        return $this->generateDocument->execute(
            $data,
            $actor,
            DocumentOrigin::ReportCard,
            $content,
        );
    }

    /**
     * @param  list<\App\Domains\AcademicCommunication\Domain\References\AssessmentSummaryReference>  $results
     *
     * @return list<array<string, mixed>>
     */
    private function serializeResults(array $results): array
    {
        return array_map(
            static fn ($result): array => [
                'subject' => $result->subject->name,
                'average' => $result->average,
                'best' => $result->bestScore,
                'worst' => $result->worstScore,
                'assessment_count' => $result->assessmentCount,
                'pass_rate' => $result->passRate,
            ],
            $results,
        );
    }

    /**
     * @param  list<\App\Domains\AcademicCommunication\Domain\References\GradebookEntryReference>  $gradebook
     *
     * @return list<array<string, mixed>>
     */
    private function serializeGradebook(array $gradebook): array
    {
        return array_map(
            static fn ($entry): array => [
                'subject' => $entry->subject->name,
                'average' => $entry->average,
                'coefficient' => $entry->coefficient,
                'weighted_average' => $entry->weightedAverage,
                'letter_grade' => $entry->letterGrade,
                'appreciation' => $entry->appreciation,
                'rank' => $entry->rank,
            ],
            $gradebook,
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function serializeAttendance(\App\Domains\AcademicCommunication\Domain\References\AttendanceSummaryReference $attendance): array
    {
        return [
            'expected_days' => $attendance->expectedDays,
            'present_days' => $attendance->presentDays,
            'absent_days' => $attendance->absentDays,
            'late_days' => $attendance->lateDays,
            'rate' => $attendance->attendanceRate,
            'period' => $attendance->periodLabel,
        ];
    }
}
