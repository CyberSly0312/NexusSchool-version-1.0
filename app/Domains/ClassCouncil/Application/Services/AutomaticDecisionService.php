<?php

declare(strict_types=1);

namespace App\Domains\ClassCouncil\Application\Services;

use App\Domains\ClassCouncil\Domain\Enums\CouncilDecisionType;
use App\Domains\ClassCouncil\Domain\References\AutomaticDecisionReference;
use App\Domains\ClassCouncil\Domain\References\GradebookStandingReference;

/**
 * Derives the automatic decision a council is expected to confirm (SPR-013).
 *
 * The automatic decision is computed exclusively from the published academic
 * standing: a student with a failing subject is automatically failed, a student
 * whose overall percentage reaches the passing threshold is automatically
 * passed. No standing means no automatic decision — the council decides
 * without one. The Class Council engine owns this rule; it is never duplicated
 * elsewhere.
 */
final class AutomaticDecisionService
{
    public function propose(?GradebookStandingReference $standing): ?AutomaticDecisionReference
    {
        if ($standing === null || $standing->overallPercentage === null) {
            return null;
        }

        if ($standing->failedSubjects > 0) {
            return new AutomaticDecisionReference(CouncilDecisionType::Failed);
        }

        $passingPercentage = (float) config('nexus-class-council.promotion.passing_percentage', 50.0);

        if ($standing->overallPercentage >= $passingPercentage) {
            return new AutomaticDecisionReference(CouncilDecisionType::Passed);
        }

        return new AutomaticDecisionReference(CouncilDecisionType::Failed);
    }
}
