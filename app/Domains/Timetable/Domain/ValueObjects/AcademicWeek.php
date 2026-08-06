<?php

declare(strict_types=1);

namespace App\Domains\Timetable\Domain\ValueObjects;

use DateTimeImmutable;
use InvalidArgumentException;

/**
 * A Monday-based teaching week identified by its start date ("Y-m-d").
 * The week spans Monday through Sunday.
 */
final readonly class AcademicWeek
{
    public function __construct(
        public string $weekStart,
    ) {
        $date = DateTimeImmutable::createFromFormat('Y-m-d', $this->weekStart);

        if ($date === false) {
            throw new InvalidArgumentException('AcademicWeek start must use the Y-m-d format.');
        }

        if ((int) $date->format('N') !== 1) {
            throw new InvalidArgumentException('AcademicWeek start must be a Monday.');
        }
    }

    public static function from(string $weekStart): self
    {
        return new self($weekStart);
    }

    public function weekEnd(): string
    {
        return DateTimeImmutable::createFromFormat('Y-m-d', $this->weekStart)
            ->modify('+6 days')
            ->format('Y-m-d');
    }

    public function contains(string $date): bool
    {
        $target = DateTimeImmutable::createFromFormat('Y-m-d', $date);

        if ($target === false) {
            return false;
        }

        return $target >= DateTimeImmutable::createFromFormat('Y-m-d', $this->weekStart)
            && $target <= DateTimeImmutable::createFromFormat('Y-m-d', $this->weekEnd());
    }

    public function label(): string
    {
        $start = DateTimeImmutable::createFromFormat('Y-m-d', $this->weekStart);

        return sprintf(
            'Semaine du %s au %s',
            $start->format('d/m/Y'),
            DateTimeImmutable::createFromFormat('Y-m-d', $this->weekEnd())->format('d/m/Y'),
        );
    }
}
