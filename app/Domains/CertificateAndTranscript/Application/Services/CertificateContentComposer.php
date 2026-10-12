<?php

declare(strict_types=1);

namespace App\Domains\CertificateAndTranscript\Application\Services;

use App\Domains\CertificateAndTranscript\Domain\Contracts\EnrollmentContract;
use App\Domains\CertificateAndTranscript\Domain\Contracts\ReportCardContract;
use App\Domains\CertificateAndTranscript\Domain\Contracts\StudentIdentityContract;
use App\Domains\CertificateAndTranscript\Domain\Enums\CompletionDocumentKind;
use App\Domains\CertificateAndTranscript\Domain\Exceptions\UnpublishedEvidenceException;
use App\Domains\CertificateAndTranscript\Domain\References\EnrollmentReference;
use App\Domains\CertificateAndTranscript\Domain\References\GraduationReference;
use App\Domains\CertificateAndTranscript\Domain\References\ReportCardSnapshotReference;
use App\Domains\CertificateAndTranscript\Domain\References\StudentReference;

/**
 * Composes the content snapshot of an end-of-study certificate (SPR-015).
 *
 * The certificate snapshots the graduation facts and the final academic record
 * verbatim from the published evidence. The eligibility verdict envelope and its
 * evidence citations are embedded so the official document and audit trail
 * remain reproducible.
 */
final class CertificateContentComposer
{
    public function __construct(
        private readonly ReportCardContract $reportCard,
        private readonly EnrollmentContract $enrollment,
        private readonly StudentIdentityContract $studentIdentity,
    ) {
    }

    /**
     * @param  array<string, mixed>  $verdict
     * @param  list<string>  $evidenceCitations
     *
     * @return array<string, mixed>
     */
    public function compose(string $schoolId, int|string $studentId, array $verdict, array $evidenceCitations): array
    {
        $student = $this->studentIdentity->student($schoolId, $studentId);
        $snapshots = $this->reportCard->publishedSnapshots($schoolId, $studentId);
        $enrollments = $this->enrollment->enrollmentHistory($schoolId, $studentId);
        $graduations = $this->enrollment->graduationRecords($schoolId, $studentId);

        if ($snapshots === []) {
            throw UnpublishedEvidenceException::for('published report card snapshot', $studentId);
        }

        $graduation = count($graduations) > 0 ? $graduations[count($graduations) - 1] : null;
        $finalEnrollment = $this->finalEnrollment($enrollments, $snapshots);

        return [
            'document' => [
                'kind' => CompletionDocumentKind::Certificate->value,
                'issued_at' => now()->toIso8601String(),
            ],
            'student' => $student !== null ? $this->serializeStudent($student) : ['id' => $studentId],
            'enrollment' => $finalEnrollment !== null ? $this->serializeEnrollment($finalEnrollment) : null,
            'graduation' => $graduation !== null ? $this->serializeGraduation($graduation) : null,
            'eligibility' => [
                'verdict' => $verdict['verdict'] ?? null,
                'reasons' => $verdict['reasons'] ?? [],
                'thresholds' => $verdict['thresholds'] ?? [],
            ],
            'evidence_citations' => $evidenceCitations,
            'final_period' => $this->serializeFinalPeriod($snapshots[count($snapshots) - 1]),
            'source' => [
                'origin' => CompletionDocumentKind::Certificate->value,
                'note' => 'Certificate content composed from published evidence: council decision, graduation record, policy thresholds and report-card snapshots.',
            ],
        ];
    }

    /**
     * @param  list<EnrollmentReference>  $enrollments
     * @param  list<ReportCardSnapshotReference>  $snapshots
     */
    private function finalEnrollment(array $enrollments, array $snapshots): ?EnrollmentReference
    {
        $finalSnapshot = count($snapshots) > 0 ? $snapshots[count($snapshots) - 1] : null;

        if ($finalSnapshot === null) {
            return null;
        }

        foreach (array_reverse($enrollments) as $enrollment) {
            if ((string) $enrollment->yearId === (string) $finalSnapshot->yearId) {
                return $enrollment;
            }
        }

        return null;
    }

    /**
     * @return array<string, mixed>
     */
    private function serializeFinalPeriod(ReportCardSnapshotReference $snapshot): array
    {
        return [
            'year_id' => $snapshot->yearId,
            'term_id' => $snapshot->termId,
            'academic_document_id' => $snapshot->academicDocumentId,
            'academic_document_number' => $snapshot->academicDocumentNumber,
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
    private function serializeGraduation(GraduationReference $graduation): array
    {
        return [
            'graduation_id' => $graduation->id,
            'decision' => $graduation->decision,
            'graduated_on' => $graduation->graduatedOn,
            'certificate_reference' => $graduation->certificateReference,
            'education_level' => $graduation->educationLevel,
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
