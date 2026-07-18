<?php

declare(strict_types=1);

namespace App\Domains\Transport\DTOs;

use App\Domains\Transport\Enums\MaintenanceStatus;
use App\Domains\Transport\Enums\MaintenanceType;

readonly class VehicleMaintenanceData
{
    public function __construct(
        public ?string $id,
        public string $school_id,
        public string $vehicle_id,
        public MaintenanceType $type,
        public string $description,
        public ?float $cost,
        public ?string $service_provider,
        public string $service_date,
        public ?string $next_service_date,
        public ?int $odometer_reading,
        public ?string $receipt_document,
        public MaintenanceStatus $status,
        public ?string $completed_at,
        public ?string $notes,
    ) {
    }

    public static function create(mixed $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            school_id: $data['school_id'],
            vehicle_id: $data['vehicle_id'],
            type: MaintenanceType::from($data['type']),
            description: $data['description'],
            cost: isset($data['cost']) ? (float) $data['cost'] : null,
            service_provider: $data['service_provider'] ?? null,
            service_date: $data['service_date'],
            next_service_date: $data['next_service_date'] ?? null,
            odometer_reading: $data['odometer_reading'] ?? null,
            receipt_document: $data['receipt_document'] ?? null,
            status: MaintenanceStatus::from($data['status'] ?? 'scheduled'),
            completed_at: $data['completed_at'] ?? null,
            notes: $data['notes'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'school_id' => $this->school_id,
            'vehicle_id' => $this->vehicle_id,
            'type' => $this->type->value,
            'description' => $this->description,
            'cost' => $this->cost,
            'service_provider' => $this->service_provider,
            'service_date' => $this->service_date,
            'next_service_date' => $this->next_service_date,
            'odometer_reading' => $this->odometer_reading,
            'receipt_document' => $this->receipt_document,
            'status' => $this->status->value,
            'completed_at' => $this->completed_at,
            'notes' => $this->notes,
        ];
    }
}
