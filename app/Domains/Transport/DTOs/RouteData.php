<?php

declare(strict_types=1);

namespace App\Domains\Transport\DTOs;

use App\Domains\Transport\Enums\RouteStatus;

readonly class RouteData
{
    public function __construct(
        public ?string $id,
        public string $school_id,
        public string $name,
        public ?string $description,
        public ?float $distance_km,
        public ?int $estimated_duration_minutes,
        public ?float $fare,
        public RouteStatus $status,
    ) {
    }

    public static function create(mixed $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            school_id: $data['school_id'],
            name: $data['name'],
            description: $data['description'] ?? null,
            distance_km: isset($data['distance_km']) ? (float) $data['distance_km'] : null,
            estimated_duration_minutes: isset($data['estimated_duration_minutes']) ? (int) $data['estimated_duration_minutes'] : null,
            fare: isset($data['fare']) ? (float) $data['fare'] : null,
            status: RouteStatus::from($data['status'] ?? 'active'),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'school_id' => $this->school_id,
            'name' => $this->name,
            'description' => $this->description,
            'distance_km' => $this->distance_km,
            'estimated_duration_minutes' => $this->estimated_duration_minutes,
            'fare' => $this->fare,
            'status' => $this->status->value,
        ];
    }
}
