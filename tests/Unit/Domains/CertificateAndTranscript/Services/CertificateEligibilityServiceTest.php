<?php

declare(strict_types=1);

use App\Domains\CertificateAndTranscript\Application\Services\CertificateEligibilityService;
use App\Domains\CertificateAndTranscript\Domain\Contracts\AcademicPoliciesContract;
use App\Domains\CertificateAndTranscript\Domain\Contracts\ClassCouncilContract;
use App\Domains\CertificateAndTranscript\Domain\Contracts\EnrollmentContract;
use App\Domains\CertificateAndTranscript\Domain\Contracts\ReportCardContract;
use App\Domains\CertificateAndTranscript\Domain\References\CouncilDecisionReference;
use App\Domains\CertificateAndTranscript\Domain\References\GraduationReference;
use App\Domains\CertificateAndTranscript\Domain\References\PolicyReference;
use App\Domains\CertificateAndTranscript\Domain\References\ReportCardSnapshotReference;

/**
 * Contract fakes that return fixed, non-computed evidence. The orchestrator
 * must only read thresholds verbatim and never recompute a standing.
 */
final class FakeEligibilityCouncilContract implements ClassCouncilContract
{
    public function __construct(private readonly ?CouncilDecisionReference $decision)
    {
    }

    public function publishedDecision(string $schoolId, int|string $studentId, int|string $yearId, int|string $termId): ?CouncilDecisionReference
    {
        return $this->decision;
    }
}

final class FakeEligibilityEnrollmentContract implements EnrollmentContract
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

final class FakeEligibilityPoliciesContract implements AcademicPoliciesContract
{
    public function __construct(private readonly array $policies = [])
    {
    }

    public function policy(string $schoolId, string $key): ?PolicyReference
    {
        return $this->policies[$key] ?? null;
    }
}

final class FakeEligibilityReportCardContract implements ReportCardContract
{
    public function __construct(private readonly array $snapshots = [])
    {
    }

    public function publishedSnapshots(string $schoolId, int|string $studentId): array
    {
        return $this->snapshots;
    }
}

beforeEach(function (): void {
    $this->finalSnapshot = new ReportCardSnapshotReference(
        entryId: 11,
        content: [
            'standing' => ['overall_percentage' => 78.75, 'failed_subjects' => 0],
        ],
        academicDocumentId: 99,
        academicDocumentNumber: 'DOC-2026-0001',
        yearId: 1,
        termId: 3,
    );

    $this->passedDecision = new CouncilDecisionReference(
        decisionId: 5,
        decisionType: 'passed',
        code: 'CC-2026-0001',
        label: 'Admis',
    );

    $this->graduation = new GraduationReference(
        id: 8,
        enrollmentId: 42,
        studentId: 7,
        decision: 'graduated',
        graduatedOn: '2026-07-15',
        educationLevel: 'Terminale',
    );

    $this->makeService = function (array $overrides = []): CertificateEligibilityService {
        $reportCard = new FakeEligibilityReportCardContract(
            $overrides['snapshots'] ?? [$this->finalSnapshot],
        );
        $graduations = $overrides['graduations'] ?? [$this->graduation];

        return new CertificateEligibilityService(
            council: $overrides['council'] ?? new FakeEligibilityCouncilContract($this->passedDecision),
            enrollment: new FakeEligibilityEnrollmentContract(graduations: $graduations),
            policies: $overrides['policies'] ?? new FakeEligibilityPoliciesContract(),
            reportCard: $reportCard,
        );
    };
});

test('an eligible student passes on the published evidence', function (): void {
    $verdict = ($this->makeService)()->determine('school-1', 7);

    expect($verdict['verdict'])->toBe('eligible')
        ->and($verdict['reasons'])->toBe([])
        ->and($verdict['evidence_citations'])->toHaveCount(3)
        ->and($verdict['thresholds'])->toMatchArray([
            'passing_percentage' => 50.0,
            'allowed_failed_subjects' => 0,
        ]);
});

test('a missing graduation record makes the student ineligible', function (): void {
    $verdict = ($this->makeService)(['graduations' => []])->determine('school-1', 7);

    expect($verdict['verdict'])->toBe('ineligible')
        ->and($verdict['reasons'])->toContain('No graduation record found.');
});

test('an unpublished or non-passing council decision makes the student ineligible', function (): void {
    $service = ($this->makeService)([
        'council' => new FakeEligibilityCouncilContract(new CouncilDecisionReference(
            decisionId: 6,
            decisionType: 'rejected',
            code: 'CC-2026-0002',
            label: 'Redoublant',
        )),
    ]);

    $verdict = $service->determine('school-1', 7);

    expect($verdict['verdict'])->toBe('ineligible')
        ->and($verdict['reasons'])->toContain('Council decision [rejected] is not passing.');
});

test('a council with no published decision is missing evidence', function (): void {
    $service = ($this->makeService)([
        'council' => new FakeEligibilityCouncilContract(null),
    ]);

    $verdict = $service->determine('school-1', 7);

    expect($verdict['verdict'])->toBe('ineligible')
        ->and($verdict['reasons'])->toContain('No published council decision found for the final period.');
});

test('a final percentage below the threshold denies eligibility', function (): void {
    $below = new ReportCardSnapshotReference(
        entryId: 12,
        content: ['standing' => ['overall_percentage' => 34.5, 'failed_subjects' => 0]],
        academicDocumentId: 99,
        academicDocumentNumber: 'DOC-2026-0002',
        yearId: 1,
        termId: 3,
    );

    $verdict = ($this->makeService)(['snapshots' => [$below]])->determine('school-1', 7);

    expect($verdict['verdict'])->toBe('ineligible')
        ->and($verdict['reasons'])->toContain('Final percentage [34.50] is below the passing threshold [50].');
});

test('failed subjects over the allowed count deny eligibility', function (): void {
    $withFailures = new ReportCardSnapshotReference(
        entryId: 13,
        content: ['standing' => ['overall_percentage' => 60.0, 'failed_subjects' => 2]],
        academicDocumentId: 99,
        academicDocumentNumber: 'DOC-2026-0003',
        yearId: 1,
        termId: 3,
    );

    $verdict = ($this->makeService)(['snapshots' => [$withFailures]])->determine('school-1', 7);

    expect($verdict['verdict'])->toBe('ineligible')
        ->and($verdict['reasons'])->toContain('Failed subjects [2] exceed the allowed count [0].');
});

test('with no published bulletin the student is refused for missing evidence', function (): void {
    $verdict = ($this->makeService)(['snapshots' => []])->determine('school-1', 7);

    expect($verdict['verdict'])->toBe('ineligible')
        ->and($verdict['reasons'])->toContain('No published report-card snapshot found for the final period.');
});

test('thresholds are policy driven and fall back to the engine defaults', function (): void {
    $defaults = ($this->makeService)();

    expect($defaults->thresholds('school-1'))->toMatchArray([
        'passing_percentage' => 50.0,
        'allowed_failed_subjects' => 0,
    ]);

    $configured = ($this->makeService)([
        'policies' => new FakeEligibilityPoliciesContract([
            'academic.certificate.passing_percentage' => new PolicyReference(
                key: 'academic.certificate.passing_percentage',
                label: 'Seuil de réussite du certificat',
                value: 60,
            ),
            'academic.certificate.allowed_failed_subjects' => new PolicyReference(
                key: 'academic.certificate.allowed_failed_subjects',
                label: 'Matières échouées tolérées',
                value: 1,
            ),
        ]),
    ]);

    expect($configured->thresholds('school-1'))->toMatchArray([
        'passing_percentage' => 60.0,
        'allowed_failed_subjects' => 1,
    ]);
});
