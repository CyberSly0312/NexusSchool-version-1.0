<?php

declare(strict_types=1);

namespace App\Domains\Gradebook\Application\Services;

use App\Domains\Gradebook\Domain\Enums\AcademicStanding;
use App\Domains\Gradebook\Domain\References\StandingContributionReference;
use App\Domains\Gradebook\Domain\ValueObjects\Gpa;

/**
 * Student-level academic aggregates (SPR-010).
 *
 * The GPA is the coefficient-weighted mean of the grade points of the student's
 * published grade entries; the CGPA is the same aggregation applied over every
 * published entry (no period filter). Academic standing derives from the
 * overall weighted percentage and the number of failing subjects. All formulas
 * live here — grade calculations belong exclusively to the Gradebook engine and
 * are never recomputed by the Report Card engine.
 */
final class AcademicStandingService
{
    public function __construct(
        private readonly GradeCalculationService $calculator,
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
     * Student-level academic standing derived from the published contributions.
     *
     * @param  list<StandingContributionReference>  $contributions
     */
    public function standing(array $contributions): ?AcademicStanding
    {
        $percentage = $this->overallPercentage($contributions);

        if ($percentage === null) {
            return null;
        }

        $failedSubjects = count(array_filter(
            $contributions,
            static fn (StandingContributionReference $c): bool => $c->percentage < (float) config('nexus-gradebook.standing.passing_percentage', 50.0),
        ));

        return AcademicStanding::fromResult(
            $percentage,
            $failedSubjects,
            passingPercentage: (float) config('nexus-gradebook.standing.passing_percentage', 50.0),
            distinctionPercentage: (float) config('nexus-gradebook.standing.distinction_percentage', 80.0),
            conditionalFailures: (int) config('nexus-gradebook.standing.conditional_failures', 1),
        );
    }
}
