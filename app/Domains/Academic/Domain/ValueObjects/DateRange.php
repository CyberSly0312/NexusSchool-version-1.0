<?php

declare(strict_types=1);

namespace App\Domains\Academic\Domain\ValueObjects;

use DateTimeImmutable;
use DateTimeInterface;

/**
 * Immutable inclusive date interval used to reason about calendar years, terms and
 * events without touching persistence.
 */
final readonly class DateRange
{
    public function __construct(
        public DateTimeInterface $startsOn,
        public DateTimeInterface $endsOn,
    ) {
        if ($this->startsOn > $this->endsOn) {
            throw new \InvalidArgumentException('DateRange start must not be after its end.');
        }
    }

    public static function fromStrings(string $startsOn, string $endsOn): self
    {
        return new self(
            new DateTimeImmutable($startsOn),
            new DateTimeImmutable($endsOn),
        );
    }

    public function contains(DateRange $other): bool
    {
        return $this->startsOn <= $other->startsOn && $other->endsOn <= $this->endsOn;
    }

    /**
     * Two ranges overlap when one starts before the other ends AND ends after the
     * other starts. Touching boundaries (end === other start) do not overlap.
     */
    public function overlaps(DateRange $other): bool
    {
        return $this->startsOn < $other->endsOn && $other->startsOn < $this->endsOn;
    }
}
