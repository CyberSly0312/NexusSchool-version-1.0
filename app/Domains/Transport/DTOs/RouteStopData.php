<?php

declare(strict_types=1);

namespace App\Domains\Transport\DTOs;

readonly class RouteStopData
{
    public function __construct(
        public ?string $id,
        public string $school_id,
        public string $route_id,
        public string $name,
        public int $stop_order,
        public ?float $latitude,
        public ?float $longitude,
        public ?int $estimated_time_minutes,
        public ?string $address,
        public ?string $notes,
    ) {
    }

    public static function create(mixed $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            school_id: $data['school_id'],
            route_id: $data['route_id'],
            name: $data['name'],
            stop_order: (int) $data['stop_order'],
            latitude: isset($data['latitude']) ? (float) $data['latitude'] : null,
            longitude: isset($data['longitude']) ? (float) $data['longitude'] : null,
            estimated_time_minutes: isset($data['estimated_time_minutes']) ? (int) $data['estimated_time_minutes'] : null,
            address: $data['address'] ?? null,
            notes: $data['notes'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'school_id' => $this->school_id,
            'route_id' => $this->route_id,
            'name' => $this->name,
            'stop_order' => $this->stop_order,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'estimated_time_minutes' => $this->estimated_time_minutes,
            'address' => $this->address,
            'notes' => $this->notes,
        ];
    }
}
