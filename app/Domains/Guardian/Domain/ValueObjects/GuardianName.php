<?php

declare(strict_types=1);

namespace App\Domains\Guardian\Domain\ValueObjects;

use InvalidArgumentException;

final readonly class GuardianName
{
    private function __construct(
        public string $firstName,
        public string $lastName,
        public ?string $middleName,
        public ?string $preferredName,
    ) {
    }

    public static function from(
        string $firstName,
        string $lastName,
        ?string $middleName = null,
        ?string $preferredName = null,
    ): self {
        $firstName = trim($firstName);
        $lastName = trim($lastName);

        if ($firstName === '') {
            throw new InvalidArgumentException('Guardian first name must not be empty.');
        }

        if ($lastName === '') {
            throw new InvalidArgumentException('Guardian last name must not be empty.');
        }

        return new self($firstName, $lastName, $middleName, $preferredName);
    }

    public function fullName(): string
    {
        return trim("{$this->firstName} {$this->lastName}");
    }

    public function displayName(): string
    {
        if ($this->preferredName !== null && trim($this->preferredName) !== '') {
            return trim("{$this->preferredName} {$this->lastName}");
        }

        return $this->fullName();
    }
}
