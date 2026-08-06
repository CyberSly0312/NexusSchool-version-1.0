<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Application\Actions;

use App\Domains\AcademicCommunication\Application\DTOs\GenerateDocumentData;
use App\Domains\AcademicCommunication\Application\Services\DocumentContentComposer;
use App\Domains\AcademicCommunication\Domain\Contracts\AssessmentContract;
use App\Domains\AcademicCommunication\Domain\Contracts\AttendanceContract;
use App\Domains\AcademicCommunication\Domain\Contracts\GuardianContract;
use App\Domains\AcademicCommunication\Domain\Contracts\StudentIdentityContract;
use App\Domains\AcademicCommunication\Domain\Enums\DocumentOrigin;
use App\Domains\AcademicCommunication\Domain\Exceptions\MissingDocumentContextException;
use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocument;
use App\Models\User;

/**
 * Generates the parent summary (synthèse parents) of a student (SPR-012).
 *
 * Parents are self-scoped recipients: the summary is composed from the student's
 * own data and delivered only to the guardians linked to that student through
 * the GuardianContract — never to other families.
 */
final class GenerateParentSummaryAction
{
    public function __construct(
        private readonly GenerateDocumentAction $generateDocument,
        private readonly DocumentContentComposer $composer,
        private readonly StudentIdentityContract $studentIdentity,
        private readonly GuardianContract $guardian,
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

        $guardians = $this->guardian->guardiansForStudent($data->schoolId, $data->studentId);

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
                'guardians' => array_map(
                    static fn ($guardian): array => [
                        'full_name' => $guardian->fullName,
                        'is_primary' => $guardian->isPrimary,
                    ],
                    $guardians,
                ),
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
            DocumentOrigin::ParentSummary,
            $content,
        );
    }
}
