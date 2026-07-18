<?php

declare(strict_types=1);

namespace App\Domains\Communication\DTOs;

use App\Domains\Communication\Enums\DeliveryStatus;

readonly class MessageRecipientData
{
    public function __construct(
        public ?string $id,
        public string $school_id,
        public string $message_id,
        public string $recipient_id,
        public DeliveryStatus $status,
        public ?string $read_at,
    ) {
    }

    public static function create(mixed $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            school_id: $data['school_id'],
            message_id: $data['message_id'],
            recipient_id: $data['recipient_id'],
            status: DeliveryStatus::from($data['status'] ?? 'pending'),
            read_at: $data['read_at'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'school_id' => $this->school_id,
            'message_id' => $this->message_id,
            'recipient_id' => $this->recipient_id,
            'status' => $this->status->value,
            'read_at' => $this->read_at,
        ];
    }
}
