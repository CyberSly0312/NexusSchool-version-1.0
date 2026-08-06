<?php

declare(strict_types=1);

namespace App\Domains\Gradebook\Domain\Exceptions;

/**
 * Thrown when the configured grade system (scale) is not supported by the
 * engine (SPR-010).
 */
final class UnsupportedGradeSystemException extends GradebookException
{
    public static function for(int $scale): self
    {
        return new self("The grade system scale [{$scale}] is not supported.");
    }
}
