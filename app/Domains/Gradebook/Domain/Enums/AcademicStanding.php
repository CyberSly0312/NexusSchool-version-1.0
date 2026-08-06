<?php

declare(strict_types=1);

namespace App\Domains\Gradebook\Domain\Enums;

/**
 * Student-level academic standing (SPR-010).
 *
 * Derived by AcademicStandingService from the overall weighted percentage of a
 * student's published grade entries and the number of failing subjects. The
 * thresholds are school-configurable (see `nexus-gradebook.standing`).
 */
enum AcademicStanding: string
{
    case Distinction = 'distinction';
    case Pass = 'pass';
    case Conditional = 'conditional';
    case Fail = 'fail';

    public function label(): string
    {
        return match ($this) {
            self::Distinction => 'Distinction',
            self::Pass => 'Admis',
            self::Conditional => 'Admis avec rattrapage',
            self::Fail => 'Refusé',
        };
    }

    /**
     * Maps an overall percentage and failing-subject count onto a standing.
     *
     * A student is on Distinction when the overall percentage reaches the
     * distinction threshold; otherwise Conditional when the number of failing
     * subjects does not exceed the conditional allowance; otherwise Fail when
     * the overall percentage is below the passing threshold or too many
     * subjects are failing.
     */
    public static function fromResult(
        float $percentage,
        int $failedSubjects = 0,
        float $passingPercentage = 50.0,
        float $distinctionPercentage = 80.0,
        int $conditionalFailures = 1,
    ): self {
        if ($percentage >= $distinctionPercentage && $failedSubjects === 0) {
            return self::Distinction;
        }

        if ($failedSubjects === 0 && $percentage >= $passingPercentage) {
            return self::Pass;
        }

        if ($percentage >= $passingPercentage && $failedSubjects <= $conditionalFailures) {
            return self::Conditional;
        }

        return self::Fail;
    }
}
