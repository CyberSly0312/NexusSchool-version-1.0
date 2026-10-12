<?php

declare(strict_types=1);

namespace App\Domains\CertificateAndTranscript\Application\Services;

use App\Domains\CertificateAndTranscript\Domain\Contracts\AcademicPoliciesContract;
use App\Domains\CertificateAndTranscript\Domain\Contracts\ClassCouncilContract;
use App\Domains\CertificateAndTranscript\Domain\Contracts\EnrollmentContract;
use App\Domains\CertificateAndTranscript\Domain\Contracts\ReportCardContract;

/**
 * Orchestrates the certificate eligibility verdict (SPR-015, ADR-021
 * Decision 4).
 *
 * The verdict comes exclusively from authoritative, published evidence: the
 * published council decision for the final period, the graduation record, the
 * policy-driven thresholds and the published report-card snapshots. The engine
 * never recomputes standing: thresholds are read verbatim from the final
 * published snapshot and the policy configuration.
 */
final class CertificateEligibilityService
{
    public function __construct(
        private readonly ClassCouncilContract $council,
        private readonly EnrollmentContract $enrollment,
        private readonly AcademicPoliciesContract $policies,
        private readonly ReportCardContract $reportCard,
    ) {
    }

    /**
     * @return array<string, mixed> The verdict envelope: verdict, reasons,
     *                              thresholds and evidence citations.
     */
    public function determine(string $schoolId, int|string $studentId): array
    {
        $snapshots = $this->reportCard->publishedSnapshots($schoolId, $studentId);
        $reasons = [];
        $citations = [];

        $finalSnapshot = count($snapshots) > 0 ? $snapshots[count($snapshots) - 1] : null;

        if ($finalSnapshot === null) {
            $reasons[] = 'No published report-card snapshot found for the final period.';
        } else {
            $citations[] = sprintf(
                'report_card_entry:%s (document %s)',
                $finalSnapshot->entryId,
                (string) $finalSnapshot->academicDocumentNumber,
            );
        }

        $councilDecision = $finalSnapshot !== null
            ? $this->council->publishedDecision(
                $schoolId,
                $studentId,
                (int) $finalSnapshot->yearId,
                (int) $finalSnapshot->termId,
            )
            : null;

        if ($councilDecision === null) {
            $reasons[] = 'No published council decision found for the final period.';
        } elseif (! $councilDecision->isPassed()) {
            $reasons[] = sprintf('Council decision [%s] is not passing.', $councilDecision->decisionType);
        } else {
            $citations[] = sprintf('council_decision:%s', $councilDecision->decisionId);
        }

        $graduations = $this->enrollment->graduationRecords($schoolId, $studentId);
        $graduation = count($graduations) > 0 ? $graduations[count($graduations) - 1] : null;

        if ($graduation === null) {
            $reasons[] = 'No graduation record found.';
        } elseif (! $graduation->isGraduated()) {
            $reasons[] = sprintf('Graduation decision [%s] is not a graduation.', $graduation->decision);
        } else {
            $citations[] = sprintf('enrollment_graduation:%s', $graduation->id);
        }

        $thresholds = $this->thresholds($schoolId);

        if ($finalSnapshot !== null) {
            $standing = $finalSnapshot->content['standing'] ?? null;
            $percentage = is_array($standing) ? (float) ($standing['overall_percentage'] ?? 0.0) : null;

            if ($percentage === null) {
                $reasons[] = 'No final standing percentage available in the published snapshot.';
            } elseif ($percentage < $thresholds['passing_percentage']) {
                $reasons[] = sprintf(
                    'Final percentage [%s] is below the passing threshold [%s].',
                    number_format($percentage, 2),
                    $thresholds['passing_percentage'],
                );
            }

            $failedSubjects = is_array($standing) ? (int) ($standing['failed_subjects'] ?? 0) : 0;
            if ($failedSubjects > $thresholds['allowed_failed_subjects']) {
                $reasons[] = sprintf(
                    'Failed subjects [%s] exceed the allowed count [%s].',
                    $failedSubjects,
                    $thresholds['allowed_failed_subjects'],
                );
            }
        }

        return [
            'verdict' => $reasons === [] ? 'eligible' : 'ineligible',
            'reasons' => $reasons,
            'thresholds' => $thresholds,
            'evidence_citations' => $citations,
        ];
    }

    /**
     * Resolve the policy-driven thresholds for the school, falling back to the
     * engine defaults when the school did not define the policy.
     *
     * @return array{passing_percentage: float, allowed_failed_subjects: int}
     */
    public function thresholds(string $schoolId): array
    {
        $config = config('nexus-certificate-and-transcript.certificate_eligibility');

        $passingPercentage = $config['default_passing_percentage'] ?? 50;
        $policy = $this->policies->policy($schoolId, $config['passing_percentage_key'] ?? 'academic.certificate.passing_percentage');
        if ($policy !== null && is_numeric($policy->value)) {
            $passingPercentage = (float) $policy->value;
        }

        $allowedFailedSubjects = $config['default_allowed_failed_subjects'] ?? 0;
        $policy = $this->policies->policy($schoolId, $config['allowed_failed_subjects_key'] ?? 'academic.certificate.allowed_failed_subjects');
        if ($policy !== null && is_numeric($policy->value)) {
            $allowedFailedSubjects = (int) $policy->value;
        }

        return [
            'passing_percentage' => $passingPercentage,
            'allowed_failed_subjects' => $allowedFailedSubjects,
        ];
    }
}
