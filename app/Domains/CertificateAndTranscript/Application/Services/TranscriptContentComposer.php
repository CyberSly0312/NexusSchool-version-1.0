<?php

declare(strict_types=1);

namespace App\Domains\CertificateAndTranscript\Application\Services;

use App\Domains\CertificateAndTranscript\Domain\Contracts\EnrollmentContract;
use App\Domains\CertificateAndTranscript\Domain\Contracts\ReportCardContract;
use App\Domains\CertificateAndTranscript\Domain\Contracts\StudentIdentityContract;
use App\Domains\CertificateAndTranscript\Domain\Enums\CompletionDocumentKind;
use App\Domains\CertificateAndTranscript\Domain\Exceptions\UnpublishedEvidenceException;
use App\Domains\CertificateAndTranscript\Domain\References\EnrollmentReference;
use App\Domains\CertificateAndTranscript\Domain\References\ReportCardSnapshotReference;
use App\Domains\CertificateAndTranscript\Domain\References\StudentReference;

/**
 * Composes the content snapshot of a transcript (SPR-015, ADR-021 Decision 2).
 *
 * The engine never performs any calculation: every published report-card
 * snapshot is read verbatim and serialized into the entry content. Terms
 * without a published report card are excluded and explicitly marked partial —
 * an issuance with zero published snapshots is refused outright.
 */
final class TranscriptContentComposer
{
    public function __construct(
        private readonly ReportCardContract $reportCard,
        private readonly EnrollmentContract $enrollment,
        private readonly StudentIdentityContract $studentIdentity,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function compose(string $schoolId, int|string $studentId): array
    {
        $student = $this->studentIdentity->student($schoolId, $studentId);
        $snapshots = $this->reportCard->publishedSnapshots($schoolId, $studentId);
        $enrollments = $this->enrollment->enrollmentHistory($schoolId, $studentId);

        if ($snapshots === []) {
            throw UnpublishedEvidenceException::for('published report card snapshot', $studentId);
        }

        $publishedYearIds = array_map(
            static fn (ReportCardSnapshotReference $snapshot): string => (string) $snapshot->yearId,
            $snapshots,
        );

        $excludedPeriods = array_values(array_filter(
            $enrollments,
            static fn (EnrollmentReference $enrollment): bool => ! in_array(
                (string) $enrollment->yearId,
                $publishedYearIds,
                true,
            ),
        ));

        return [
            'document' => [
                'kind' => CompletionDocumentKind::Transcript->value,
                'issued_at' => now()->toIso8601String(),
            ],
            'student' => $student !== null ? $this->serializeStudent($student) : ['id' => $studentId],
            'periods' => array_map(
                fn (ReportCardSnapshotReference $snapshot): array => $this->serializeSnapshot($snapshot),
                $snapshots,
            ),
            'enrollment' => array_map(
                fn (EnrollmentReference $enrollment): array => $this->serializeEnrollment($enrollment),
                $enrollments,
            ),
            'partial' => count($enrollments) > 0 && count($excludedPeriods) > 0,
            'excluded_periods' => array_map(
                fn (EnrollmentReference $enrollment): array => $this->serializeEnrollment($enrollment),
                $excludedPeriods,
            ),
            'source' => [
                'origin' => CompletionDocumentKind::Transcript->value,
                'note' => 'Assembled verbatim from published report-card snapshots; never recomputed.',
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function serializeSnapshot(ReportCardSnapshotReference $snapshot): array
    {
        return [
            'academic_document_id' => $snapshot->academicDocumentId,
            'academic_document_number' => $snapshot->academicDocumentNumber,
            'year_id' => $snapshot->yearId,
            'term_id' => $snapshot->termId,
            'snapshot' => $snapshot->content,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function serializeEnrollment(EnrollmentReference $enrollment): array
    {
        return [
            'enrollment_id' => $enrollment->id,
            'year_id' => $enrollment->yearId,
            'class' => $enrollment->className,
            'section' => $enrollment->sectionName,
            'education_level' => $enrollment->educationLevel,
            'status' => $enrollment->status,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function serializeStudent(StudentReference $student): array
    {
        return [
            'id' => $student->id,
            'full_name' => $student->fullName,
            'student_number' => $student->studentNumber,
            'date_of_birth' => $student->dateOfBirth,
            'gender' => $student->gender,
        ];
    }
}
