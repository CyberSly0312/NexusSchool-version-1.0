<?php

declare(strict_types=1);

namespace App\Domains\Gradebook\Application\Services;

use App\Domains\Gradebook\Domain\Contracts\AcademicPoliciesContract;
use App\Domains\Gradebook\Domain\Enums\AcademicStanding;
use App\Domains\Gradebook\Domain\References\StandingContributionReference;
use App\Domains\Gradebook\Domain\ValueObjects\Gpa;

/**
 * Student-level academic aggregates (SPR-010).
 *
 * The GPA is the coefficient-weighted mean of the grade points of the student's
 * published grade entries; the CGPA is the same aggregation applied over every
 * published entry (no period filter). Academic standing derives from the
 * overall weighted percentage and the number of failing subjects. The standing
 * thresholds are policy-driven per school through the Academic Policies engine,
 * falling back to the configurable engine defaults when the school did not
 * define a policy. All formulas live here — grade calculations belong
 * exclusively to the Gradebook engine and are never recomputed by the Report
 * Card engine.
 */
final class AcademicStandingService
{
    public function __construct(
        private readonly GradeCalculationService $calculator,
        private readonly AcademicPoliciesContract $policies,
    ) {
    }

    /**
     * @param  list<StandingContributionReference>  $contributions
     */
    public function gpa(array $contributions, ?float $scale = null): ?Gpa
    {
        return Gpa::weighted(
            array_map(static fn (StandingContributionReference $c): float => $c->gradePoint, $contributions),
            array_map(static fn (StandingContributionReference $c): float => $c->coefficient, $contributions),
            scale: $scale ?? (float) config('nexus-gradebook.gpa.scale', 4.0),
            decimalPlaces: (int) config('nexus-gradebook.gpa.decimal_places', 2),
        );
    }

    /**
     * The CGPA is the GPA over every published contribution, with no period
     * filter. The caller decides the scope: pass a full-year list for the CGPA
     * and a single-period list for the term GPA — the formula is identical.
     *
     * @param  list<StandingContributionReference>  $contributions
     */
    public function cgpa(array $contributions, ?float $scale = null): ?Gpa
    {
        return $this->gpa($contributions, $scale);
    }

    /**
     * Coefficient-weighted overall percentage of the published contributions.
     *
     * @param  list<StandingContributionReference>  $contributions
     */
    public function overallPercentage(array $contributions): ?float
    {
        if ($contributions === []) {
            return null;
        }

        return $this->calculator->calculateWeightedMean(
            array_map(static fn (StandingContributionReference $c): float => $c->percentage, $contributions),
            array_map(static fn (StandingContributionReference $c): float => $c->coefficient, $contributions),
        );
    }

    /**
     * Number of failing subjects of a student: contributions below the
     * policy-driven passing threshold of the school.
     *
     * @param  list<StandingContributionReference>  $contributions
     */
    public function failedSubjectCount(array $contributions, string $schoolId): int
    {
        $passingPercentage = $this->passingPercentage($schoolId);

        return count(array_filter(
            $contributions,
            static fn (StandingContributionReference $c): bool => $c->percentage < $passingPercentage,
        ));
    }

    /**
     * Student-level academic standing derived from the published contributions,
     * using the per-school policy-driven thresholds.
     *
     * @param  list<StandingContributionReference>  $contributions
     */
    public function standing(array $contributions, string $schoolId): ?AcademicStanding
    {
        $percentage = $this->overallPercentage($contributions);

        if ($percentage === null) {
            return null;
        }

        return AcademicStanding::fromResult(
            $percentage,
            $this->failedSubjectCount($contributions, $schoolId),
            passingPercentage: $this->passingPercentage($schoolId),
            distinctionPercentage: $this->distinctionPercentage($schoolId),
            conditionalFailures: $this->conditionalFailures($schoolId),
        );
    }

    /**
     * Resolve the per-school passing threshold, falling back to the engine
     * default when the school did not define the policy.
     */
    private function passingPercentage(string $schoolId): float
    {
        return $this->resolveFloat($schoolId, 'passing_percentage_key', 'passing_percentage', 50.0);
    }

    /**
     * Resolve the per-school distinction threshold, falling back to the engine
     * default when the school did not define the policy.
     */
    private function distinctionPercentage(string $schoolId): float
    {
        return $this->resolveFloat($schoolId, 'distinction_percentage_key', 'distinction_percentage', 80.0);
    }

    /**
     * Resolve the per-school conditional-failure allowance, falling back to the
     * engine default when the school did not define the policy.
     */
    private function conditionalFailures(string $schoolId): int
    {
        return $this->resolveInt($schoolId, 'conditional_failures_key', 'conditional_failures', 1);
    }

    private function resolveFloat(string $schoolId, string $keyNode, string $defaultNode, float $fallback): float
    {
        $config = config('nexus-gradebook.standing', []);
        $policy = $this->policies->policy($schoolId, (string) ($config[$keyNode] ?? ''));

        if ($policy !== null && is_numeric($policy->value)) {
            return (float) $policy->value;
        }

        return (float) ($config[$defaultNode] ?? $fallback);
    }

    private function resolveInt(string $schoolId, string $keyNode, string $defaultNode, int $fallback): int
    {
        $config = config('nexus-gradebook.standing', []);
        $policy = $this->policies->policy($schoolId, (string) ($config[$keyNode] ?? ''));

        if ($policy !== null && is_numeric($policy->value)) {
            return (int) $policy->value;
        }

        return (int) ($config[$defaultNode] ?? $fallback);
    }
}
