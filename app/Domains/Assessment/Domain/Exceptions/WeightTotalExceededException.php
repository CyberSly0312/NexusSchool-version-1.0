<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Domain\Exceptions;

/**
 * Thrown when the weight ceiling policy would be exceeded by an assessment
 * (SPR-009). The ceiling comes from the nexus-assessment configuration.
 */
final class WeightTotalExceededException extends AssessmentException
{
    public static function forTotal(float $total, float $ceiling): self
    {
        return new self("The sum of weights [{$total}] exceeds the school ceiling [{$ceiling}].");
    }
}
