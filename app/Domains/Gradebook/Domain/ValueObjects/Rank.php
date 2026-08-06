<?php

declare(strict_types=1);

namespace App\Domains\Gradebook\Domain\ValueObjects;

use InvalidArgumentException;

/**
 * Immutable rank position of a student inside a class (SPR-010). The
 * denominator is the number of ranked students; rank 1 is the best.
 */
final readonly class Rank
{
    private function __construct(
        public int $position,
        public int $denominator,
    ) {
    }

    public static function fromPosition(int $position, int $denominator): self
    {
        if ($position < 1) {
            throw new InvalidArgumentException("A rank must be at least 1, [{$position}] given.");
        }

        if ($denominator < 1) {
            throw new InvalidArgumentException("The rank denominator must be at least 1, [{$denominator}] given.");
        }

        return new self($position, $denominator);
    }

    public function __toString(): string
    {
        return "{$this->position}/{$this->denominator}";
    }
}
