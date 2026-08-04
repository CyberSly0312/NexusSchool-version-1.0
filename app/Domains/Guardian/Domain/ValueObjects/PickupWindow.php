<?php

declare(strict_types=1);

namespace App\Domains\Guardian\Domain\ValueObjects;

use DateTimeImmutable;
use InvalidArgumentException;

/**
 * Validity window of an authorized-pickup grant. The window is optional: when
 * both dates are null the authorization is open-ended.
 */
final readonly class PickupWindow
{
    public function __construct(
        public ?string $validFrom = null,
        public ?string $validUntil = null,
    ) {
        $this->assertValid();
    }

    public function isValidAt(?string $date = null): bool
    {
        $target = $date !== null ? DateTimeImmutable::createFromFormat('Y-m-d', $date) : new DateTimeImmutable();

        if ($target === false) {
            return false;
        }

        if ($this->validFrom !== null) {
            $from = DateTimeImmutable::createFromFormat('Y-m-d', $this->validFrom);
            if ($from !== false && $target < $from) {
                return false;
            }
        }

        if ($this->validUntil !== null) {
            $until = DateTimeImmutable::createFromFormat('Y-m-d', $this->validUntil);
            if ($until !== false && $target > $until) {
                return false;
            }
        }

        return true;
    }

    private function assertValid(): void
    {
        $from = $this->validFrom !== null ? DateTimeImmutable::createFromFormat('Y-m-d', $this->validFrom) : null;
        $until = $this->validUntil !== null ? DateTimeImmutable::createFromFormat('Y-m-d', $this->validUntil) : null;

        if ($from === false || $until === false) {
            throw new InvalidArgumentException('Pickup window dates must use the Y-m-d format.');
        }

        if ($from !== null && $until !== null && $from > $until) {
            throw new InvalidArgumentException('The pickup window start date must not be after the end date.');
        }
    }
}
