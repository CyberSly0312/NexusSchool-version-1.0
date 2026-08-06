<?php

declare(strict_types=1);

namespace App\Domains\Staff\Domain\ValueObjects;

use DateTimeImmutable;
use InvalidArgumentException;

/**
 * Validity period of an employment contract. The end date, when provided,
 * must not precede the start date.
 */
final readonly class ContractPeriod
{
    public function __construct(
        public string $startDate,
        public ?string $endDate = null,
    ) {
        $this->assertValid();
    }

    public function isCurrent(?string $date = null): bool
    {
        $target = $date !== null
            ? DateTimeImmutable::createFromFormat('Y-m-d', $date)
            : new DateTimeImmutable();

        if ($target === false) {
            return false;
        }

        $start = DateTimeImmutable::createFromFormat('Y-m-d', $this->startDate);

        if ($start === false || $target < $start) {
            return false;
        }

        if ($this->endDate !== null) {
            $end = DateTimeImmutable::createFromFormat('Y-m-d', $this->endDate);

            if ($end === false || $target > $end) {
                return false;
            }
        }

        return true;
    }

    private function assertValid(): void
    {
        $start = DateTimeImmutable::createFromFormat('Y-m-d', $this->startDate);

        if ($start === false) {
            throw new InvalidArgumentException('Contract start date must use the Y-m-d format.');
        }

        if ($this->endDate !== null) {
            $end = DateTimeImmutable::createFromFormat('Y-m-d', $this->endDate);

            if ($end === false) {
                throw new InvalidArgumentException('Contract end date must use the Y-m-d format.');
            }

            if ($end < $start) {
                throw new InvalidArgumentException('Contract end date must not precede the start date.');
            }
        }
    }
}
