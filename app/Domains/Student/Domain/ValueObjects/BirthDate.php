<?php

declare(strict_types=1);

namespace App\Domains\Student\Domain\ValueObjects;

use DateTimeImmutable;
use InvalidArgumentException;

final readonly class BirthDate
{
    private function __construct(public DateTimeImmutable $date)
    {
    }

    public static function from(DateTimeImmutable|string $date): self
    {
        $instance = new self(is_string($date) ? new DateTimeImmutable($date) : $date);

        if ($instance->date > new DateTimeImmutable('today')) {
            throw new InvalidArgumentException('Birth date cannot be in the future.');
        }

        return $instance;
    }

    public function age(): int
    {
        return $this->date->diff(new DateTimeImmutable('today'))->y;
    }

    public function toDateString(): string
    {
        return $this->date->format('Y-m-d');
    }
}
