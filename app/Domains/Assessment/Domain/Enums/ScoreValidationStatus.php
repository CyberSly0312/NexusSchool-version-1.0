<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Domain\Enums;

/**
 * Result of the score validation pass (SPR-009).
 *
 * A score is Valid when it is a finite number inside [0, maximum_score].
 * BelowPassing is a legitimate fail (not an error); AboveMaximum and
 * BelowZero are invalid and must be corrected.
 */
enum ScoreValidationStatus: string
{
    case Valid = 'valid';
    case BelowPassing = 'below_passing';
    case AboveMaximum = 'above_maximum';
    case BelowZero = 'below_zero';

    public function label(): string
    {
        return match ($this) {
            self::Valid => 'Valide',
            self::BelowPassing => 'Sous le seuil de réussite',
            self::AboveMaximum => 'Au-dessus du maximum',
            self::BelowZero => 'Négative',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Valid => 'success',
            self::BelowPassing => 'warning',
            self::AboveMaximum => 'danger',
            self::BelowZero => 'danger',
        };
    }

    public function isInvalid(): bool
    {
        return $this === self::AboveMaximum || $this === self::BelowZero;
    }
}
