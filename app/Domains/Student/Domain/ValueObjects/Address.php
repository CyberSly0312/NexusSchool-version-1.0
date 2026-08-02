<?php

declare(strict_types=1);

namespace App\Domains\Student\Domain\ValueObjects;

final readonly class Address
{
    public function __construct(
        public ?string $street,
        public ?string $city,
        public ?string $state,
        public ?string $postalCode,
        public ?string $country,
    ) {
    }

    public static function fromParts(
        ?string $street = null,
        ?string $city = null,
        ?string $state = null,
        ?string $postalCode = null,
        ?string $country = null,
    ): self {
        return new self(
            street: self::nullable(trim((string) $street)),
            city: self::nullable(trim((string) $city)),
            state: self::nullable(trim((string) $state)),
            postalCode: self::nullable(trim((string) $postalCode)),
            country: self::nullable(trim((string) $country)),
        );
    }

    public function isComplete(): bool
    {
        return $this->street !== null && $this->city !== null;
    }

    public function formatted(): string
    {
        $parts = array_filter([
            $this->street,
            $this->city,
            $this->state,
            $this->postalCode,
            $this->country,
        ], fn (?string $part): bool => $part !== null && $part !== '');

        return implode(', ', $parts);
    }

    private static function nullable(string $value): ?string
    {
        return $value === '' ? null : $value;
    }
}
