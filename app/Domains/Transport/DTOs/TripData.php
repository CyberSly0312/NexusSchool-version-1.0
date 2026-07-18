<?php

declare(strict_types=1);

namespace App\Domains\Transport\DTOs;

use App\Domains\Transport\Enums\TripStatus;

readonly class TripData
{
    public function __construct(
        public ?string $id,
        public string $school_id,
        public string $vehicle_id,
        public string $driver_id,
        public string $route_id,
        public string $direction,
        public string $academic_date,
        public ?string $departure_time,
        public ?string $arrival_time,
        public ?string $actual_departure,
        public ?string $actual_arrival,
        public TripStatus $status,
        public ?string $notes,
    ) {
    }

    public static function create(mixed $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            school_id: $data['school_id'],
            vehicle_id: $data['vehicle_id'],
            driver_id: $data['driver_id'],
            route_id: $data['route_id'],
            direction: $data['direction'],
            academic_date: $data['academic_date'],
            departure_time: $data['departure_time'] ?? null,
            arrival_time: $data['arrival_time'] ?? null,
            actual_departure: $data['actual_departure'] ?? null,
            actual_arrival: $data['actual_arrival'] ?? null,
            status: TripStatus::from($data['status'] ?? 'scheduled'),
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
            'route_id' => $this->route_id,
            'direction' => $this->direction,
            'academic_date' => $this->academic_date,
            'departure_time' => $this->departure_time,
            'arrival_time' => $this->arrival_time,
            'actual_departure' => $this->actual_departure,
            'actual_arrival' => $this->actual_arrival,
            'status' => $this->status->value,
            'notes' => $this->notes,
        ];
    }
}
