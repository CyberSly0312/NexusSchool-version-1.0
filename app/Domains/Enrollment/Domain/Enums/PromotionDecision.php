<?php

declare(strict_types=1);

namespace App\Domains\Enrollment\Domain\Enums;

/**
 * Outcome of an end-of-year promotion/repetition decision.
 *
 * Every decision creates a NEW enrollment row (promotion and repetition
 * preserve the previous row as immutable history).
 */
enum PromotionDecision: string
{
    case Promoted = 'promoted';
    case Repeated = 'repeated';
    case Skipped = 'skipped';

    public function label(): string
    {
        return match ($this) {
            self::Promoted => 'Admis',
            self::Repeated => 'Redouble',
            self::Skipped => 'Sauté une classe',
        };
    }
}
