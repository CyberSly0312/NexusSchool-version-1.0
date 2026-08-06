<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Application\Services;

use App\Domains\Assessment\Domain\Enums\ScoreValidationStatus;
use App\Domains\Assessment\Domain\ValueObjects\MaximumScore;
use App\Domains\Assessment\Domain\ValueObjects\PassingScore;

/**
 * Pure validation rules for a single score (SPR-009).
 *
 * A score is valid when it is a finite number inside [0, maximum_score]. A
 * score below the passing score is a legitimate fail (BelowPassing), not an
 * error. Out-of-range scores are invalid and must be corrected.
 */
class ScoreValidationService
{
    public function validate(float $score, float $maximum, ?float $passing = null): ScoreValidationStatus
    {
        MaximumScore::fromFloat($maximum);

        if (is_nan($score) || is_infinite($score)) {
            return ScoreValidationStatus::BelowZero;
        }

        if ($score < 0.0) {
            return ScoreValidationStatus::BelowZero;
        }

        if ($score > $maximum) {
            return ScoreValidationStatus::AboveMaximum;
        }

        if ($passing !== null) {
            PassingScore::fromFloat($passing, $maximum);

            if ($score < $passing) {
                return ScoreValidationStatus::BelowPassing;
            }
        }

        return ScoreValidationStatus::Valid;
    }
}
