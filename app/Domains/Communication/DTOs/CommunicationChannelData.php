<?php

declare(strict_types=1);

namespace App\Domains\Communication\DTOs;

use App\Domains\Communication\Enums\NotificationChannel;

readonly class CommunicationChannelData
{
    public function __construct(
        public ?string $id,
        public string $school_id,
        public string $name,
        public NotificationChannel $channel_type,
        public bool $is_active,
        public ?array $config,
        public ?string $from_address,
        public ?string $from_name,
    ) {
    }

    public static function create(mixed $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            school_id: $data['school_id'],
            name: $data['name'],
            channel_type: NotificationChannel::from($data['channel_type']),
            is_active: $data['is_active'] ?? true,
            config: $data['config'] ?? null,
            from_address: $data['from_address'] ?? null,
            from_name: $data['from_name'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'school_id' => $this->school_id,
            'name' => $this->name,
            'channel_type' => $this->channel_type->value,
            'is_active' => $this->is_active,
            'config' => $this->config,
            'from_address' => $this->from_address,
            'from_name' => $this->from_name,
        ];
    }
}
