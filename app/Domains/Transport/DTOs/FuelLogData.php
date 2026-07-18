<?php

declare(strict_types=1);

namespace App\Domains\Transport\DTOs;

readonly class FuelLogData
{
    public function __construct(
        public ?string $id,
        public string $school_id,
        public string $vehicle_id,
        public ?string $driver_id,
        public float $liters,
        public float $cost_per_liter,
        public float $total_cost,
        public string $fuel_type,
        public ?string $fuel_station,
        public int $odometer_reading,
        public string $fuel_date,
        public ?string $receipt_document,
        public ?string $notes,
    ) {
    }

    public static function create(mixed $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            school_id: $data['school_id'],
            vehicle_id: $data['vehicle_id'],
            driver_id: $data['driver_id'] ?? null,
            liters: (float) $data['liters'],
            cost_per_liter: (float) $data['cost_per_liter'],
            total_cost: (float) $data['total_cost'],
            fuel_type: $data['fuel_type'],
            fuel_station: $data['fuel_station'] ?? null,
            odometer_reading: (int) $data['odometer_reading'],
            fuel_date: $data['fuel_date'],
            receipt_document: $data['receipt_document'] ?? null,
            notes: $data['notes'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'school_id' => $this->school_id,
            'vehicle_id' => $this->vehicle_id,
            'driver_id' => $this->driver_id,
            'liters' => $this->liters,
            'cost_per_liter' => $this->cost_per_liter,
            'total_cost' => $this->total_cost,
            'fuel_type' => $this->fuel_type,
            'fuel_station' => $this->fuel_station,
            'odometer_reading' => $this->odometer_reading,
            'fuel_date' => $this->fuel_date,
            'receipt_document' => $this->receipt_document,
            'notes' => $this->notes,
        ];
    }
}
