<?php

declare(strict_types=1);

namespace App\Domains\Academic\Domain\ValueObjects;

/**
 * Grading weight of a subject inside a class. Strictly positive, up to 999.99.
 */
final readonly class Coefficient
{
    public function __construct(
        public float $value,
        public bool $isOptional = false,
    ) {
        if ($value <= 0.0 || $value > 999.99) {
            throw new \InvalidArgumentException('Coefficient must be between 0.01 and 999.99.');
        }
    }

    public function value(): float
    {
        return $this->value;
    }

    public function isOptional(): bool
    {
        return $this->isOptional;
    }

    public function __toString(): string
    {
        return number_format($this->value, 2, '.', '');
    }
}
