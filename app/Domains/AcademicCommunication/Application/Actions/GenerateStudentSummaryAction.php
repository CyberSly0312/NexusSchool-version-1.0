<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Application\Actions;

use App\Domains\AcademicCommunication\Application\DTOs\GenerateDocumentData;
use App\Domains\AcademicCommunication\Application\Services\DocumentContentComposer;
use App\Domains\AcademicCommunication\Domain\Contracts\AssessmentContract;
use App\Domains\AcademicCommunication\Domain\Contracts\AttendanceContract;
use App\Domains\AcademicCommunication\Domain\Contracts\StudentIdentityContract;
use App\Domains\AcademicCommunication\Domain\Enums\DocumentOrigin;
use App\Domains\AcademicCommunication\Domain\Exceptions\MissingDocumentContextException;
use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocument;
use App\Models\User;

/**
 * Generates the student summary (synthèse élève) (SPR-012). Student-scoped: the
 * document is composed from the student's own results and attendance and is
 * delivered to the student themselves through the visibility policy.
 */
final class GenerateStudentSummaryAction
{
    public function __construct(
        private readonly GenerateDocumentAction $generateDocument,
        private readonly DocumentContentComposer $composer,
        private readonly StudentIdentityContract $studentIdentity,
        private readonly AssessmentContract $assessment,
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

        $attendance = $this->attendance->summaryForStudent(
            $data->schoolId,
            $data->studentId,
            $data->yearId,
            $data->termId,
        );

        $content = array_merge(
            $this->composer->schoolBlock($data->schoolId),
            $this->composer->frameBlock($data->schoolId, $data->yearId, $data->termId, $data->classId, $data->sectionId),
            [
                'student' => [
                    'id' => $student->id,
                    'full_name' => $student->fullName,
                    'student_number' => $student->studentNumber,
                    'class' => $student->class,
                    'section' => $student->section,
                ],
                'results' => array_map(
                    static fn ($result): array => [
                        'subject' => $result->subject->name,
                        'average' => $result->average,
                        'pass_rate' => $result->passRate,
                    ],
                    $results,
                ),
                'attendance' => $attendance !== null
                    ? [
                        'expected_days' => $attendance->expectedDays,
                        'present_days' => $attendance->presentDays,
                        'absent_days' => $attendance->absentDays,
                        'rate' => $attendance->attendanceRate,
                    ]
                    : null,
            ],
        );

        return $this->generateDocument->execute(
            $data,
            $actor,
            DocumentOrigin::StudentSummary,
            $content,
        );
    }
}
