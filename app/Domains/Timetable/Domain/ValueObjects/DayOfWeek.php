<?php

declare(strict_types=1);

namespace App\Domains\Timetable\Domain\ValueObjects;

use InvalidArgumentException;

/**
 * Day of the teaching week. 0 = Monday .. 6 = Sunday, following the European
 * school convention. Immutable by construction.
 */
final readonly class DayOfWeek
{
    public const MONDAY = 0;
    public const TUESDAY = 1;
    public const WEDNESDAY = 2;
    public const THURSDAY = 3;
    public const FRIDAY = 4;
    public const SATURDAY = 5;
    public const SUNDAY = 6;

    public function __construct(
        public int $value,
    ) {
        if ($value < self::MONDAY || $value > self::SUNDAY) {
            throw new InvalidArgumentException("DayOfWeek must be between 0 and 6, got [{$value}].");
        }
    }

    public static function from(int $value): self
    {
        return new self($value);
    }

    /**
     * Convert Carbon's day-of-week (0 = Sunday) to the timetable convention.
     */
    public static function fromCarbonDay(int $carbonDay): self
    {
        return new self(($carbonDay + 6) % 7);
    }

    public function label(): string
    {
        return match ($this->value) {
            self::MONDAY => 'Lundi',
            self::TUESDAY => 'Mardi',
            self::WEDNESDAY => 'Mercredi',
            self::THURSDAY => 'Jeudi',
            self::FRIDAY => 'Vendredi',
            self::SATURDAY => 'Samedi',
            self::SUNDAY => 'Dimanche',
        };
    }

    public function shortLabel(): string
    {
        return match ($this->value) {
            self::MONDAY => 'Lun',
            self::TUESDAY => 'Mar',
            self::WEDNESDAY => 'Mer',
            self::THURSDAY => 'Jeu',
            self::FRIDAY => 'Ven',
            self::SATURDAY => 'Sam',
            self::SUNDAY => 'Dim',
        };
    }
}
