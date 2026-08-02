<?php

declare(strict_types=1);

namespace App\Domains\Student\Domain\ValueObjects;

use InvalidArgumentException;

final readonly class EmailAddress
{
    public const MAX_LENGTH = 255;

    private string $value;

    public function __construct(string $value)
    {
        $value = mb_strtolower(trim($value));

        if ($value === '') {
            throw new InvalidArgumentException('Email address must not be empty.');
        }

        if (mb_strlen($value) > self::MAX_LENGTH) {
            throw new InvalidArgumentException('Email address must not exceed '.self::MAX_LENGTH.' characters.');
        }

        if (! filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Email address is not a valid format.');
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
