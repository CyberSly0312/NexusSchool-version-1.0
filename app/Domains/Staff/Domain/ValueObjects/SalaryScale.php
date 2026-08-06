<?php

declare(strict_types=1);

namespace App\Domains\Staff\Domain\ValueObjects;

use InvalidArgumentException;

/**
 * Salary scale identifier (e.g. S01..S12) used to reference a pay band
 * without coupling the staff engine to the future payroll module.
 */
final readonly class SalaryScale
{
    private string $value;

    public function __construct(string $value)
    {
        $value = strtoupper(trim($value));

        if ($value === '') {
            throw new InvalidArgumentException('Salary scale must not be empty.');
        }

        if (! preg_match('/^S\d{2}$/', $value)) {
            throw new InvalidArgumentException('Salary scale must use the S01..S99 format.');
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
