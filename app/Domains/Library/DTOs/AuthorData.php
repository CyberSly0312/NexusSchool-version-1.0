<?php

declare(strict_types=1);

namespace App\Domains\Library\DTOs;

readonly class AuthorData
{
    public function __construct(
        public string $schoolId,
        public string $firstName,
        public string $lastName,
        public ?string $middleName = null,
        public ?string $biography = null,
        public ?string $nationality = null,
        public ?string $dateOfBirth = null,
        public ?string $photo = null,
        public ?string $website = null,
        public string $status = 'active',
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            schoolId: $data['school_id'],
            firstName: $data['first_name'],
            lastName: $data['last_name'],
            middleName: $data['middle_name'] ?? null,
            biography: $data['biography'] ?? null,
            nationality: $data['nationality'] ?? null,
            dateOfBirth: $data['date_of_birth'] ?? null,
            photo: $data['photo'] ?? null,
            website: $data['website'] ?? null,
            status: $data['status'] ?? 'active',
        );
    }

    public function toArray(): array
    {
        return [
            'school_id' => $this->schoolId,
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'middle_name' => $this->middleName,
            'biography' => $this->biography,
            'nationality' => $this->nationality,
            'date_of_birth' => $this->dateOfBirth,
            'photo' => $this->photo,
            'website' => $this->website,
            'status' => $this->status,
        ];
    }
}
