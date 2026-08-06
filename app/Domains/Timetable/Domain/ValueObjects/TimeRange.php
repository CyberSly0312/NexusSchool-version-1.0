<?php

declare(strict_types=1);

namespace App\Domains\Timetable\Domain\ValueObjects;

use InvalidArgumentException;

/**
 * Immutable start/end pair on a single day, expressed in "H:i" format.
 * A range is always non-empty (start strictly before end).
 */
final readonly class TimeRange
{
    public function __construct(
        public string $startsAt,
        public string $endsAt,
    ) {
        $this->assertValid();
    }

    public static function from(string $startsAt, string $endsAt): self
    {
        return new self($startsAt, $endsAt);
    }

    public function durationMinutes(): int
    {
        return $this->toMinutes($this->endsAt) - $this->toMinutes($this->startsAt);
    }

    /**
     * Touching boundaries (one ends exactly when the other starts) do not overlap.
     */
    public function overlaps(self $other): bool
    {
        return $this->toMinutes($this->startsAt) < $this->toMinutes($other->endsAt)
            && $this->toMinutes($other->startsAt) < $this->toMinutes($this->endsAt);
    }

    public function contains(string $time): bool
    {
        $minutes = $this->toMinutes($time);

        return $minutes >= $this->toMinutes($this->startsAt) && $minutes <= $this->toMinutes($this->endsAt);
    }

    private function toMinutes(string $time): int
    {
        $parts = explode(':', $time);

        if (count($parts) !== 2 || ! is_numeric($parts[0]) || ! is_numeric($parts[1])) {
            throw new InvalidArgumentException("Time must use the H:i format, got [{$time}].");
        }

        return ((int) $parts[0]) * 60 + (int) $parts[1];
    }

    private function assertValid(): void
    {
        $start = $this->toMinutes($this->startsAt);
        $end = $this->toMinutes($this->endsAt);

        if ($start >= $end) {
            throw new InvalidArgumentException(
                "TimeRange start [{$this->startsAt}] must be before its end [{$this->endsAt}]."
            );
        }
    }
}
