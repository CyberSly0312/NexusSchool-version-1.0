<?php

declare(strict_types=1);

namespace App\Domains\Gradebook\Domain\Exceptions;

/**
 * Thrown when a percentage outside [0, 100] is fed to the grade engine
 * (SPR-010).
 */
final class InvalidPercentageException extends GradebookException
{
    public static function for(float $percentage): self
    {
        return new self("Invalid percentage [{$percentage}]; expected a value inside [0, 100].");
    }
}
