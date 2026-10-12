<?php

declare(strict_types=1);

use App\Domains\CertificateAndTranscript\Application\Services\TranscriptContentComposer;
use App\Domains\CertificateAndTranscript\Domain\Contracts\EnrollmentContract;
use App\Domains\CertificateAndTranscript\Domain\Contracts\ReportCardContract;
use App\Domains\CertificateAndTranscript\Domain\Contracts\StudentIdentityContract;
use App\Domains\CertificateAndTranscript\Domain\Enums\CompletionDocumentKind;
use App\Domains\CertificateAndTranscript\Domain\Exceptions\UnpublishedEvidenceException;
use App\Domains\CertificateAndTranscript\Domain\References\EnrollmentReference;
use App\Domains\CertificateAndTranscript\Domain\References\ReportCardSnapshotReference;
use App\Domains\CertificateAndTranscript\Domain\References\StudentReference;

/**
 * Contract fakes that return fixed, non-computed snapshots. The composer must
 * serialize them verbatim and never perform any calculation.
 */
final class FakeTranscriptReportCardContract implements ReportCardContract
{
    public function __construct(private readonly array $snapshots)
    {
    }

    public function publishedSnapshots(string $schoolId, int|string $studentId): array
    {
        return $this->snapshots;
    }
}

final class FakeTranscriptEnrollmentContract implements EnrollmentContract
{
    public function __construct(
        private readonly array $enrollments = [],
        private readonly array $graduations = [],
    ) {
    }

    public function enrollmentHistory(string $schoolId, int|string $studentId): array
    {
        return $this->enrollments;
    }

    public function graduationRecords(string $schoolId, int|string $studentId): array
    {
        return $this->graduations;
    }
}

final class FakeTranscriptStudentIdentityContract implements StudentIdentityContract
{
    public function __construct(private readonly ?StudentReference $student)
    {
    }

    public function student(string $schoolId, int|string $studentId): ?StudentReference
    {
        return $this->student;
    }
}

beforeEach(function (): void {
    $this->snapshotContent = [
        'student' => ['id' => 7, 'full_name' => 'Jean Dupont'],
        'period' => ['year_id' => 1, 'term_id' => 2],
        'gradebook' => [
            ['subject_name' => 'Mathématiques', 'average' => 15.75],
        ],
        'standing' => ['overall_percentage' => 78.75, 'failed_subjects' => 0],
    ];

    $this->snapshot = new ReportCardSnapshotReference(
        entryId: 11,
        content: $this->snapshotContent,
        academicDocumentId: 99,
        academicDocumentNumber: 'DOC-2026-0001',
        yearId: 1,
        termId: 2,
    );

    $this->enrollment = new EnrollmentReference(
        id: 42,
        studentId: 7,
        yearId: 1,
        className: '3e A',
        sectionName: null,
        educationLevel: 'Collège',
        status: 'active',
    );

    $this->composer = new TranscriptContentComposer(
        reportCard: new FakeTranscriptReportCardContract([$this->snapshot]),
        enrollment: new FakeTranscriptEnrollmentContract([$this->enrollment]),
        studentIdentity: new FakeTranscriptStudentIdentityContract(
            new StudentReference(
                id: 7,
                fullName: 'Jean Dupont',
                studentNumber: 'STU-2026-0001',
                dateOfBirth: '2010-05-01',
                gender: 'male',
            ),
        ),
    );
});

test('the transcript serializes every published snapshot verbatim', function (): void {
    $content = $this->composer->compose('school-1', 7);

    expect($content['document']['kind'])->toBe(CompletionDocumentKind::Transcript->value)
        ->and($content['partial'])->toBeFalse()
        ->and($content['excluded_periods'])->toBe([])
        ->and($content['periods'])->toHaveCount(1)
        ->and($content['periods'][0]['year_id'])->toBe(1)
        ->and($content['periods'][0]['term_id'])->toBe(2)
        ->and($content['periods'][0]['academic_document_id'])->toBe(99)
        ->and($content['periods'][0]['academic_document_number'])->toBe('DOC-2026-0001')
        ->and($content['periods'][0]['snapshot'])->toBe($this->snapshotContent)
        ->and($content['enrollment'])->toHaveCount(1)
        ->and($content['enrollment'][0]['enrollment_id'])->toBe(42)
        ->and($content['student']['full_name'])->toBe('Jean Dupont')
        ->and($content['source']['note'])->toContain('never recomputed');
});

test('terms without a published report card are excluded and marked partial', function (): void {
    $composer = new TranscriptContentComposer(
        reportCard: new FakeTranscriptReportCardContract([$this->snapshot]),
        enrollment: new FakeTranscriptEnrollmentContract([
            $this->enrollment,
            new EnrollmentReference(id: 43, studentId: 7, yearId: 2, className: '3e A', status: 'active'),
        ]),
        studentIdentity: new FakeTranscriptStudentIdentityContract(null),
    );

    $content = $composer->compose('school-1', 7);

    expect($content['partial'])->toBeTrue()
        ->and($content['periods'])->toHaveCount(1)
        ->and($content['excluded_periods'])->toHaveCount(1)
        ->and($content['excluded_periods'][0]['year_id'])->toBe(2)
        ->and($content['excluded_periods'][0]['enrollment_id'])->toBe(43);
});

test('an issuance with zero published snapshots is refused', function (): void {
    $composer = new TranscriptContentComposer(
        reportCard: new FakeTranscriptReportCardContract([]),
        enrollment: new FakeTranscriptEnrollmentContract([$this->enrollment]),
        studentIdentity: new FakeTranscriptStudentIdentityContract(null),
    );

    expect(fn () => $composer->compose('school-1', 7))
        ->toThrow(UnpublishedEvidenceException::class);
});
