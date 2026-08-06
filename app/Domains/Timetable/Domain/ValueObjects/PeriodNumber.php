<?php

declare(strict_types=1);

namespace App\Domains\Timetable\Domain\ValueObjects;

use InvalidArgumentException;

/**
 * 1-based ordinal position of a teaching period within the day.
 */
final readonly class PeriodNumber
{
    public function __construct(
        public int $value,
    ) {
        if ($value < 1) {
            throw new InvalidArgumentException("PeriodNumber must be 1-based, got [{$value}].");
        }
    }

    public static function from(int $value): self
    {
        return new self($value);
    }

    public function label(): string
    {
        return sprintf('P%d', $this->value);
    }
}
