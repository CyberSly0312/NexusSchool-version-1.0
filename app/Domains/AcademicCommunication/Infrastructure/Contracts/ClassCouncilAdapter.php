<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Infrastructure\Contracts;

use App\Domains\AcademicCommunication\Domain\Contracts\ClassCouncilContract;
use App\Domains\AcademicCommunication\Domain\References\CouncilDecisionReference;
use App\Domains\ClassCouncil\Domain\Enums\CouncilSessionStatus;
use App\Domains\ClassCouncil\Infrastructure\Models\CouncilDecision;
use App\Domains\ClassCouncil\Infrastructure\Models\CouncilSession;

/**
 * ClassCouncilContract implementation (SPR-012, powered by SPR-013).
 *
 * Exposes only the *published* decisions of one council session. Confidential
 * deliberations never cross this boundary and can never reach a rendered
 * document.
 */
final class ClassCouncilAdapter implements ClassCouncilContract
{
    public function publishedDecisions(
        string $schoolId,
        int|string $sessionId,
    ): array {
        $session = CouncilSession::query()
            ->forSchool($schoolId)
            ->whereKey($sessionId)
            ->whereIn('status', [CouncilSessionStatus::Published->value, CouncilSessionStatus::Archived->value])
            ->first();

        if ($session === null) {
            return [];
        }

        return CouncilDecision::query()
            ->forSchool($schoolId)
            ->where('council_session_id', $sessionId)
            ->orderBy('id')
            ->get()
            ->map(fn (CouncilDecision $decision): CouncilDecisionReference => new CouncilDecisionReference(
                id: $decision->getKey(),
                summary: $decision->summary ?? $decision->label,
                category: $decision->category?->value ?? 'general',
                studentId: $decision->student_id,
                decisionDate: $decision->decision_date?->toDateString(),
                recommendation: $decision->recommendation,
            ))
            ->all();
    }
}
