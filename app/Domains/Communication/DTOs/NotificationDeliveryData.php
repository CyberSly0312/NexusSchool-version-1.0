<?php

declare(strict_types=1);

namespace App\Domains\Communication\DTOs;

use App\Domains\Communication\Enums\DeliveryStatus;
use App\Domains\Communication\Enums\NotificationChannel;

readonly class NotificationDeliveryData
{
    public function __construct(
        public ?string $id,
        public string $school_id,
        public string $notification_id,
        public string $recipient_id,
        public NotificationChannel $channel,
        public DeliveryStatus $status,
        public ?string $sent_at,
        public ?string $delivered_at,
        public ?string $failed_at,
        public ?string $error_message,
        public ?array $metadata,
    ) {
    }

    public static function create(mixed $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            school_id: $data['school_id'],
            notification_id: $data['notification_id'],
            recipient_id: $data['recipient_id'],
            channel: NotificationChannel::from($data['channel']),
            status: DeliveryStatus::from($data['status'] ?? 'pending'),
            sent_at: $data['sent_at'] ?? null,
            delivered_at: $data['delivered_at'] ?? null,
            failed_at: $data['failed_at'] ?? null,
            error_message: $data['error_message'] ?? null,
            metadata: $data['metadata'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'school_id' => $this->school_id,
            'notification_id' => $this->notification_id,
            'recipient_id' => $this->recipient_id,
            'channel' => $this->channel->value,
            'status' => $this->status->value,
            'sent_at' => $this->sent_at,
            'delivered_at' => $this->delivered_at,
            'failed_at' => $this->failed_at,
            'error_message' => $this->error_message,
            'metadata' => $this->metadata,
        ];
    }
}
