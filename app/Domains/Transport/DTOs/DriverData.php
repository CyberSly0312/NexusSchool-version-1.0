<?php

declare(strict_types=1);

namespace App\Domains\Transport\DTOs;

use App\Domains\Transport\Enums\DriverStatus;

readonly class DriverData
{
    public function __construct(
        public ?string $id,
        public string $school_id,
        public string $first_name,
        public string $last_name,
        public ?string $email,
        public string $phone,
        public string $license_number,
        public string $license_expiry,
        public ?string $license_category,
        public ?string $address,
        public ?string $emergency_contact_name,
        public ?string $emergency_contact_phone,
        public ?string $date_of_birth,
        public ?string $national_id,
        public ?string $photo,
        public DriverStatus $status,
        public ?string $notes,
    ) {
    }

    public static function create(mixed $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            school_id: $data['school_id'],
            first_name: $data['first_name'],
            last_name: $data['last_name'],
            email: $data['email'] ?? null,
            phone: $data['phone'],
            license_number: $data['license_number'],
            license_expiry: $data['license_expiry'],
            license_category: $data['license_category'] ?? null,
            address: $data['address'] ?? null,
            emergency_contact_name: $data['emergency_contact_name'] ?? null,
            emergency_contact_phone: $data['emergency_contact_phone'] ?? null,
            date_of_birth: $data['date_of_birth'] ?? null,
            national_id: $data['national_id'] ?? null,
            photo: $data['photo'] ?? null,
            status: DriverStatus::from($data['status'] ?? 'available'),
            notes: $data['notes'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'school_id' => $this->school_id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'license_number' => $this->license_number,
            'license_expiry' => $this->license_expiry,
            'license_category' => $this->license_category,
            'address' => $this->address,
            'emergency_contact_name' => $this->emergency_contact_name,
            'emergency_contact_phone' => $this->emergency_contact_phone,
            'date_of_birth' => $this->date_of_birth,
            'national_id' => $this->national_id,
            'photo' => $this->photo,
            'status' => $this->status->value,
            'notes' => $this->notes,
        ];
    }
}
