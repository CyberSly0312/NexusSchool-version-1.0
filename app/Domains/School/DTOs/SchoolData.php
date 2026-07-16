<?php

namespace App\Domains\School\DTOs;

use App\Domains\School\Enums\SchoolStatus;
use App\Domains\School\Enums\SchoolType;
use Illuminate\Support\Str;

readonly class SchoolData
{
    public function __construct(
        public string $name,
        public ?string $slug = null,
        public ?string $database = null,
        public ?string $domain = null,
        public ?string $email = null,
        public ?string $phone = null,
        public ?string $address = null,
        public ?string $city = null,
        public ?string $state = null,
        public ?string $country = null,
        public ?string $postalCode = null,
        public string $timezone = 'UTC',
        public string $currency = 'USD',
        public string $locale = 'en',
        public ?string $logoUrl = null,
        public ?string $registrationNumber = null,
        public SchoolType $schoolType = SchoolType::Secondary,
        public SchoolStatus $status = SchoolStatus::Pending,
        public ?array $settings = null,
        public ?array $metadata = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            slug: $data['slug'] ?? Str::slug($data['name']),
            database: $data['database'] ?? 'school_'.Str::slug($data['name']),
            domain: $data['domain'] ?? null,
            email: $data['email'] ?? null,
            phone: $data['phone'] ?? null,
            address: $data['address'] ?? null,
            city: $data['city'] ?? null,
            state: $data['state'] ?? null,
            country: $data['country'] ?? null,
            postalCode: $data['postal_code'] ?? null,
            timezone: $data['timezone'] ?? 'UTC',
            currency: $data['currency'] ?? 'USD',
            locale: $data['locale'] ?? 'en',
            logoUrl: $data['logo_url'] ?? null,
            registrationNumber: $data['registration_number'] ?? null,
            schoolType: isset($data['school_type'])
                ? SchoolType::from($data['school_type'])
                : SchoolType::Secondary,
            status: isset($data['status'])
                ? SchoolStatus::from($data['status'])
                : SchoolStatus::Pending,
            settings: $data['settings'] ?? null,
            metadata: $data['metadata'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'slug' => $this->slug,
            'database' => $this->database,
            'domain' => $this->domain,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'country' => $this->country,
            'postal_code' => $this->postalCode,
            'timezone' => $this->timezone,
            'currency' => $this->currency,
            'locale' => $this->locale,
            'logo_url' => $this->logoUrl,
            'registration_number' => $this->registrationNumber,
            'school_type' => $this->schoolType->value,
            'status' => $this->status->value,
            'settings' => $this->settings,
            'metadata' => $this->metadata,
        ];
    }
}
