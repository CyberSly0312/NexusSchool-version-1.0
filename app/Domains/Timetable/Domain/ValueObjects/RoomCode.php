<?php

declare(strict_types=1);

namespace App\Domains\Timetable\Domain\ValueObjects;

use InvalidArgumentException;

/**
 * Canonical, uppercase, alphanumeric identifier of a room. Free of any reference
 * to a concrete room model: the future Room Management engine owns rooms.
 */
final readonly class RoomCode
{
    public function __construct(
        public string $value,
    ) {
        if (preg_match('/^[A-Za-z0-9-]{1,36}$/', $value) !== 1) {
            throw new InvalidArgumentException("RoomCode must be alphanumeric (dashes allowed), got [{$value}].");
        }
    }

    public static function from(string $value): self
    {
        return new self(strtoupper($value));
    }

    public static function isSupported(string $value): bool
    {
        return preg_match('/^[A-Za-z0-9-]{1,36}$/', $value) === 1;
    }

    public function __toString(): string
    {
        return strtoupper($this->value);
    }
}
