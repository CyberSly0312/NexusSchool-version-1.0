<?php

declare(strict_types=1);

namespace App\Domains\Student\Domain\ValueObjects;

use InvalidArgumentException;

final readonly class Nationality
{
    public const MAX_LENGTH = 100;

    private string $value;

    public function __construct(string $value)
    {
        $value = trim($value);

        if ($value === '') {
            throw new InvalidArgumentException('Nationality must not be empty.');
        }

        if (mb_strlen($value) > self::MAX_LENGTH) {
            throw new InvalidArgumentException('Nationality must not exceed '.self::MAX_LENGTH.' characters.');
        }

        $this->value = $value;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
