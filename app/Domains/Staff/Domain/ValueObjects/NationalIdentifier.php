<?php

declare(strict_types=1);

namespace App\Domains\Staff\Domain\ValueObjects;

use InvalidArgumentException;

/**
 * National identification number of a staff member.
 */
final readonly class NationalIdentifier
{
    private string $value;

    public function __construct(string $value)
    {
        $value = trim($value);

        if ($value === '') {
            throw new InvalidArgumentException('National identifier must not be empty.');
        }

        if (mb_strlen($value) > 50) {
            throw new InvalidArgumentException('National identifier must not exceed 50 characters.');
        }

        if (! preg_match('/^[A-Za-z0-9]+$/', $value)) {
            throw new InvalidArgumentException('National identifier may only contain letters and digits.');
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
