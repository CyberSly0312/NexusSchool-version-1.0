<?php

declare(strict_types=1);

namespace App\Domains\Student\Domain\ValueObjects;

use InvalidArgumentException;

final readonly class EmergencyContact
{
    public string $name;

    public string $phone;

    public ?string $relationship;

    public function __construct(string $name, string $phone, ?string $relationship)
    {
        $name = trim($name);
        $phone = trim($phone);

        if ($name === '') {
            throw new InvalidArgumentException('Emergency contact name must not be empty.');
        }

        new PhoneNumber($phone);

        $this->name = $name;
        $this->phone = $phone;
        $this->relationship = $relationship !== null ? trim($relationship) : null;
    }

    public static function fromNullable(?string $name, ?string $phone, ?string $relationship): ?self
    {
        if ($name === null || trim($name) === '') {
            return null;
        }

        return new self($name, (string) $phone, $relationship);
    }
}
