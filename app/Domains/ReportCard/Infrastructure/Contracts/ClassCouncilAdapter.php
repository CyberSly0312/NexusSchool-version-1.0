<?php

declare(strict_types=1);

namespace App\Domains\ReportCard\Infrastructure\Contracts;

use App\Domains\ClassCouncil\Domain\Enums\CouncilSessionStatus;
use App\Domains\ClassCouncil\Infrastructure\Models\CouncilDecision;
use App\Domains\ClassCouncil\Infrastructure\Models\CouncilSession;
use App\Domains\Enrollment\Domain\Enums\EnrollmentStatus;
use App\Domains\Enrollment\Infrastructure\Models\Enrollment;
use App\Domains\ReportCard\Domain\Contracts\ClassCouncilContract;
use App\Domains\ReportCard\Domain\References\CouncilDecisionReference;

/**
 * ClassCouncilContract implementation (SPR-011, powered by SPR-013).
 *
 * Projects the *published* decisions of the latest council session covering the
 * student's class and period. Confidential deliberations never cross this
 * boundary and can never appear on a bulletin.
 */
final class ClassCouncilAdapter implements ClassCouncilContract
{
    public function decisionsForStudent(
        string $schoolId,
        int|string $studentId,
        int|string $yearId,
        int|string $termId,
    ): array {
        $session = $this->latestSessionForStudent($schoolId, $studentId, $yearId, $termId);

        if ($session === null) {
            return [];
        }

        return CouncilDecision::query()
            ->forSchool($schoolId)
            ->where('council_session_id', $session->getKey())
            ->where('student_id', $studentId)
            ->orderBy('id')
            ->get()
            ->map(fn (CouncilDecision $decision): CouncilDecisionReference => new CouncilDecisionReference(
                code: $decision->code,
                label: $decision->label,
                decision: $decision->decision_type->value,
            ))
            ->all();
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
