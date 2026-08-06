<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Domain\Exceptions;

/**
 * Thrown when a score that is already published (and therefore immutable) is
 * targeted by an update (SPR-009).
 */
final class ScoreNotMutableException extends AssessmentException
{
    public static function forScore(int|string $scoreId): self
    {
        return new self("Score [{$scoreId}] is published and can no longer be modified.");
    }
}
