<?php

declare(strict_types=1);

namespace App\Domains\Transport\DTOs;

use App\Domains\Transport\Enums\VehicleStatus;

readonly class VehicleData
{
    public function __construct(
        public ?string $id,
        public string $school_id,
        public string $registration_number,
        public string $vin,
        public string $manufacturer,
        public string $model,
        public int $year,
        public ?string $color,
        public int $capacity,
        public string $fuel_type,
        public ?string $insurance_expiry,
        public ?string $inspection_expiry,
        public ?string $insurance_document,
        public VehicleStatus $status,
        public ?string $notes,
    ) {
    }

    public static function create(mixed $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            school_id: $data['school_id'],
            registration_number: $data['registration_number'],
            vin: $data['vin'],
            manufacturer: $data['manufacturer'],
            model: $data['model'],
            year: $data['year'],
            color: $data['color'] ?? null,
            capacity: $data['capacity'],
            fuel_type: $data['fuel_type'],
            insurance_expiry: $data['insurance_expiry'] ?? null,
            inspection_expiry: $data['inspection_expiry'] ?? null,
            insurance_document: $data['insurance_document'] ?? null,
            status: VehicleStatus::from($data['status'] ?? 'available'),
            notes: $data['notes'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'school_id' => $this->school_id,
            'registration_number' => $this->registration_number,
            'vin' => $this->vin,
            'manufacturer' => $this->manufacturer,
            'model' => $this->model,
            'year' => $this->year,
            'color' => $this->color,
            'capacity' => $this->capacity,
            'fuel_type' => $this->fuel_type,
            'insurance_expiry' => $this->insurance_expiry,
            'inspection_expiry' => $this->inspection_expiry,
            'insurance_document' => $this->insurance_document,
            'status' => $this->status->value,
            'notes' => $this->notes,
        ];
    }
}
