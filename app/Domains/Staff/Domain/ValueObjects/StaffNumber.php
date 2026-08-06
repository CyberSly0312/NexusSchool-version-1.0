<?php

declare(strict_types=1);

namespace App\Domains\Staff\Domain\ValueObjects;

use InvalidArgumentException;

/**
 * Staff identifier in the canonical STF-PREFIX-SEQUENCE format.
 * Format: STF-{6 hex chars}-{zero-padded 4 digit sequence}.
 */
final readonly class StaffNumber
{
    public const PREFIX = 'STF';

    private string $value;

    public function __construct(string $value)
    {
        $value = trim($value);

        if ($value === '') {
            throw new InvalidArgumentException('Staff number must not be empty.');
        }

        if (! preg_match('/^STF-[0-9A-F]{6}-\d{4}$/', $value)) {
            throw new InvalidArgumentException('Staff number must use the STF-XXXXXX-0000 format.');
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
