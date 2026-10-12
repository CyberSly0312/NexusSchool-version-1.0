<?php

declare(strict_types=1);

namespace App\Domains\ClassCouncil\Application\Services;

use App\Domains\ClassCouncil\Domain\Contracts\AcademicPoliciesContract;
use App\Domains\ClassCouncil\Domain\Enums\CouncilDecisionType;
use App\Domains\ClassCouncil\Domain\References\AutomaticDecisionReference;
use App\Domains\ClassCouncil\Domain\References\GradebookStandingReference;

/**
 * Derives the automatic decision a council is expected to confirm (SPR-013).
 *
 * The automatic decision is computed exclusively from the published academic
 * standing: a student with a failing subject is automatically failed, a student
 * whose overall percentage reaches the promotion passing threshold is
 * automatically passed. No standing means no automatic decision — the council
 * decides without one. The passing threshold is policy-driven per school
 * through the Academic Policies engine, falling back to the configurable engine
 * default when the school did not define a policy. The Class Council engine
 * owns this rule; it is never duplicated elsewhere.
 */
final class AutomaticDecisionService
{
    public function __construct(
        private readonly AcademicPoliciesContract $policies,
    ) {
    }

    public function propose(?GradebookStandingReference $standing, string $schoolId): ?AutomaticDecisionReference
    {
        if ($standing === null || $standing->overallPercentage === null) {
            return null;
        }

        if ($standing->failedSubjects > 0) {
            return new AutomaticDecisionReference(CouncilDecisionType::Failed);
        }

        $passingPercentage = $this->passingPercentage($schoolId);

        if ($standing->overallPercentage >= $passingPercentage) {
            return new AutomaticDecisionReference(CouncilDecisionType::Passed);
        }

        return new AutomaticDecisionReference(CouncilDecisionType::Failed);
    }

    /**
     * Resolve the per-school promotion passing threshold, falling back to the
     * engine default when the school did not define the policy.
     */
    private function passingPercentage(string $schoolId): float
    {
        $config = config('nexus-class-council.promotion', []);
        $policy = $this->policies->policy(
            $schoolId,
            (string) ($config['passing_percentage_key'] ?? 'academic.class-council.passing_percentage'),
        );

        if ($policy !== null && is_numeric($policy->value)) {
            return (float) $policy->value;
        }

        return (float) ($config['passing_percentage'] ?? 50.0);
    }
}
