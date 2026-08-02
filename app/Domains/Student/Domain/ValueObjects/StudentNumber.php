<?php

declare(strict_types=1);

namespace App\Domains\Student\Domain\ValueObjects;

use InvalidArgumentException;

final readonly class StudentNumber
{
    public const MAX_LENGTH = 50;

    private string $value;

    public function __construct(string $value)
    {
        $value = trim($value);

        if ($value === '') {
            throw new InvalidArgumentException('Student number must not be empty.');
        }

        if (mb_strlen($value) > self::MAX_LENGTH) {
            throw new InvalidArgumentException('Student number must not exceed '.self::MAX_LENGTH.' characters.');
        }

        $this->value = mb_strtoupper($value);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
