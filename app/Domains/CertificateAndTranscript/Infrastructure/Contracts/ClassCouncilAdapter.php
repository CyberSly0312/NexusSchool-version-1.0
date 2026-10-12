<?php

declare(strict_types=1);

namespace App\Domains\CertificateAndTranscript\Infrastructure\Contracts;

use App\Domains\CertificateAndTranscript\Domain\Contracts\ClassCouncilContract;
use App\Domains\CertificateAndTranscript\Domain\References\CouncilDecisionReference;
use App\Domains\ClassCouncil\Domain\Enums\CouncilDecisionType;
use App\Domains\ClassCouncil\Domain\Enums\CouncilSessionStatus;
use App\Domains\ClassCouncil\Infrastructure\Models\CouncilDecision;
use App\Domains\ClassCouncil\Infrastructure\Models\CouncilSession;
use App\Domains\Enrollment\Domain\Enums\EnrollmentStatus;
use App\Domains\Enrollment\Infrastructure\Models\Enrollment;

/**
 * ClassCouncilContract implementation (SPR-015, sourced from SPR-013).
 *
 * Projects only the decision of the latest *published* council session covering
 * the student's class and final period. Unpublished sessions never cross this
 * boundary (ADR-013).
 */
final class ClassCouncilAdapter implements ClassCouncilContract
{
    public function publishedDecision(
        string $schoolId,
        int|string $studentId,
        int|string $yearId,
        int|string $termId,
    ): ?CouncilDecisionReference {
        $session = $this->latestSessionForStudent($schoolId, $studentId, $yearId, $termId);

        if ($session === null) {
            return null;
        }

        $decision = CouncilDecision::query()
            ->forSchool($schoolId)
            ->where('council_session_id', $session->getKey())
            ->where('student_id', $studentId)
            ->orderBy('id')
            ->first();

        if ($decision === null) {
            return null;
        }

        $decisionType = $decision->getAttribute('decision_type');
        $decisionType = $decisionType instanceof CouncilDecisionType ? $decisionType->value : ($decisionType === null ? null : (string) $decisionType);

        return new CouncilDecisionReference(
            decisionId: $decision->getKey(),
            decisionType: $decisionType,
            code: $decision->code,
            label: $decision->label,
        );
    }

    private function latestSessionForStudent(
        string $schoolId,
        int|string $studentId,
        int|string $yearId,
        int|string $termId,
    ): ?CouncilSession {
        $enrollment = Enrollment::query()
            ->forSchool($schoolId)
            ->where('student_id', $studentId)
            ->where('academic_calendar_year_id', $yearId)
            ->where('status', EnrollmentStatus::Active->value)
            ->first();

        if ($enrollment === null) {
            return null;
        }

        return CouncilSession::query()
            ->forSchool($schoolId)
            ->where('academic_calendar_year_id', $yearId)
            ->where('academic_calendar_term_id', $termId)
            ->where('school_class_id', $enrollment->school_class_id)
            ->where(function ($query) use ($enrollment): void {
                $query->where('class_section_id', $enrollment->class_section_id)
                    ->orWhereNull('class_section_id');
            })
            ->whereIn('status', [CouncilSessionStatus::Published->value, CouncilSessionStatus::Archived->value])
            ->orderByDesc('published_at')
            ->first();
    }
}
