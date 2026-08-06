<?php

declare(strict_types=1);

namespace App\Domains\Staff\Domain\ValueObjects;

use InvalidArgumentException;

/**
 * Bank account used for salary payments.
 */
final readonly class BankAccount
{
    private string $value;

    public function __construct(string $value)
    {
        $value = trim($value);

        if ($value === '') {
            throw new InvalidArgumentException('Bank account must not be empty.');
        }

        if (mb_strlen($value) > 50) {
            throw new InvalidArgumentException('Bank account must not exceed 50 characters.');
        }

        if (! preg_match('/^[A-Z]{2}[0-9]{2}[A-Za-z0-9]{4,}$/', $value)) {
            throw new InvalidArgumentException('Bank account must use a valid IBAN-like format.');
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
