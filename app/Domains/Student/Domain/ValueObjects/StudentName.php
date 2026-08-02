<?php

declare(strict_types=1);

namespace App\Domains\Student\Domain\ValueObjects;

use InvalidArgumentException;

final readonly class StudentName
{
    public string $firstName;

    public string $lastName;

    public ?string $middleName;

    public ?string $preferredName;

    public function __construct(
        string $firstName,
        string $lastName,
        ?string $middleName = null,
        ?string $preferredName = null,
    ) {
        $firstName = trim($firstName);
        $lastName = trim($lastName);

        if ($firstName === '' || $lastName === '') {
            throw new InvalidArgumentException('Both a first name and a last name are required.');
        }

        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->middleName = $middleName;
        $this->preferredName = $preferredName;
    }

    public function fullName(): string
    {
        $parts = array_filter([$this->firstName, $this->middleName, $this->lastName]);

        return implode(' ', $parts);
    }

    /**
     * The name a student prefers to be addressed by.
     */
    public function displayName(): string
    {
        if ($this->preferredName !== null && trim($this->preferredName) !== '') {
            return trim($this->preferredName).' '.$this->lastName;
        }

        return $this->fullName();
    }

    public function __toString(): string
    {
        return $this->fullName();
    }
}
