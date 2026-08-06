<?php

declare(strict_types=1);

namespace App\Domains\Timetable\Domain\ValueObjects;

use InvalidArgumentException;

/**
 * Positive lesson length in minutes. Matches the configured teaching period
 * duration of the school when scheduling regular lessons.
 */
final readonly class LessonDuration
{
    public function __construct(
        public int $minutes,
    ) {
        if ($minutes < 1) {
            throw new InvalidArgumentException("LessonDuration must be positive, got [{$minutes}].");
        }
    }

    public static function from(int $minutes): self
    {
        return new self($minutes);
    }

    public function toHours(): string
    {
        return number_format($minutes = $this->minutes / 60, 2, '.', '');
    }
}
