<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Domain\Exceptions;

/**
 * Thrown when a score is rejected because it falls outside [0, maximum_score]
 * (SPR-009).
 */
final class ScoreOutOfRangeException extends AssessmentException
{
    public static function forValue(float $score, float $maximum): self
    {
        return new self("Score [{$score}] is outside the valid range [0, {$maximum}].");
    }
}
