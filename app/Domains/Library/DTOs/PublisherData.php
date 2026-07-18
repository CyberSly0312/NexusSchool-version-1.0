<?php

declare(strict_types=1);

namespace App\Domains\Library\DTOs;

readonly class PublisherData
{
    public function __construct(
        public string $schoolId,
        public string $name,
        public ?string $address = null,
        public ?string $city = null,
        public ?string $state = null,
        public ?string $country = null,
        public ?string $postalCode = null,
        public ?string $phone = null,
        public ?string $email = null,
        public ?string $website = null,
        public ?string $notes = null,
        public string $status = 'active',
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            schoolId: $data['school_id'],
            name: $data['name'],
            address: $data['address'] ?? null,
            city: $data['city'] ?? null,
            state: $data['state'] ?? null,
            country: $data['country'] ?? null,
            postalCode: $data['postal_code'] ?? null,
            phone: $data['phone'] ?? null,
            email: $data['email'] ?? null,
            website: $data['website'] ?? null,
            notes: $data['notes'] ?? null,
            status: $data['status'] ?? 'active',
        );
    }

    public function toArray(): array
    {
        return [
            'school_id' => $this->schoolId,
            'name' => $this->name,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'country' => $this->country,
            'postal_code' => $this->postalCode,
            'phone' => $this->phone,
            'email' => $this->email,
            'website' => $this->website,
            'notes' => $this->notes,
            'status' => $this->status,
        ];
    }
}
