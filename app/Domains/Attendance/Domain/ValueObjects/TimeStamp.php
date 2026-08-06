<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Domain\ValueObjects;

use Carbon\CarbonImmutable;
use DateTimeInterface;

/**
 * Immutable instant used across the engine so time math is deterministic and
 * never mutated by consumers (SPR-008).
 */
final readonly class TimeStamp
{
    public CarbonImmutable $value;

    public function __construct(DateTimeInterface|CarbonImmutable|string $value)
    {
        $this->value = $value instanceof CarbonImmutable
            ? $value
            : CarbonImmutable::parse($value);
    }

    public static function now(): self
    {
        return new self(CarbonImmutable::now());
    }

    public function format(string $format = 'Y-m-d H:i:s'): string
    {
        return $this->value->format($format);
    }

    public function toDateString(): string
    {
        return $this->value->toDateString();
    }

    /**
     * Minutes elapsed from $reference to $this when positive, 0 otherwise.
     */
    public function minutesAfter(self $reference): int
    {
        // diffInMinutes($other) is signed as (other - this) in this Carbon
        // version, so (this - reference) is computed from the reference.
        $diff = (int) $reference->value->diffInMinutes($this->value);

        return max(0, $diff);
    }

    public function __toString(): string
    {
        return $this->format();
    }
}
