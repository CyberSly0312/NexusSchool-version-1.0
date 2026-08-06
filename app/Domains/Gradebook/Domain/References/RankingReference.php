<?php

declare(strict_types=1);

namespace App\Domains\Gradebook\Domain\References;

/**
 * The ranked position of a student inside one gradebook (SPR-010). Ranks use
 * standard competition ranking: equal percentages share a rank and the next
 * rank skips.
 */
final readonly class RankingReference
{
    public function __construct(
        public int|string $studentId,
        public float $percentage,
        public int $rank,
        public int $denominator,
    ) {
    }
}
