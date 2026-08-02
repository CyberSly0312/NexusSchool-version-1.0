<?php

declare(strict_types=1);

namespace App\Domains\Student\Domain\ValueObjects;

use InvalidArgumentException;

final readonly class PhoneNumber
{
    private string $value;

    public function __construct(string $value)
    {
        $value = trim($value);

        if ($value === '') {
            throw new InvalidArgumentException('Phone number must not be empty.');
        }

        if (! preg_match('/^[+]?[0-9][0-9\s().-]{4,30}$/', $value)) {
            throw new InvalidArgumentException('Phone number is not a valid format.');
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
