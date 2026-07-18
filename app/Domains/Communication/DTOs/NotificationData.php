<?php

declare(strict_types=1);

namespace App\Domains\Communication\DTOs;

use App\Domains\Communication\Enums\NotificationChannel;
use App\Domains\Communication\Enums\NotificationStatus;

readonly class NotificationData
{
    public function __construct(
        public ?string $id,
        public string $school_id,
        public ?string $sender_id,
        public ?string $template_id,
        public string $type,
        public string $title,
        public string $body,
        public ?array $data,
        public NotificationStatus $status,
        public NotificationChannel $channel,
        public ?string $scheduled_at,
        public ?string $sent_at,
        public ?string $delivered_at,
        public ?string $read_at,
        public int $retry_count,
        public int $max_retries,
        public ?string $last_error,
    ) {
    }

    public static function create(mixed $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            school_id: $data['school_id'],
            sender_id: $data['sender_id'] ?? null,
            template_id: $data['template_id'] ?? null,
            type: $data['type'],
            title: $data['title'],
            body: $data['body'],
            data: $data['data'] ?? null,
            status: $data['status'] instanceof NotificationStatus ? $data['status'] : NotificationStatus::from($data['status'] ?? 'pending'),
            channel: $data['channel'] instanceof NotificationChannel ? $data['channel'] : NotificationChannel::from($data['channel'] ?? 'in_app'),
            scheduled_at: $data['scheduled_at'] ?? null,
            sent_at: $data['sent_at'] ?? null,
            delivered_at: $data['delivered_at'] ?? null,
            read_at: $data['read_at'] ?? null,
            retry_count: $data['retry_count'] ?? 0,
            max_retries: $data['max_retries'] ?? 3,
            last_error: $data['last_error'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'school_id' => $this->school_id,
            'sender_id' => $this->sender_id,
            'template_id' => $this->template_id,
            'type' => $this->type,
            'title' => $this->title,
            'body' => $this->body,
            'data' => $this->data,
            'status' => $this->status instanceof NotificationStatus ? $this->status->value : $this->status,
            'channel' => $this->channel instanceof NotificationChannel ? $this->channel->value : $this->channel,
            'scheduled_at' => $this->scheduled_at,
            'sent_at' => $this->sent_at,
            'delivered_at' => $this->delivered_at,
            'read_at' => $this->read_at,
            'retry_count' => $this->retry_count,
            'max_retries' => $this->max_retries,
            'last_error' => $this->last_error,
        ];
    }
}
