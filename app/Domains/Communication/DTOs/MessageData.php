<?php

declare(strict_types=1);

namespace App\Domains\Communication\DTOs;

use App\Domains\Communication\Enums\MessageStatus;
use App\Domains\Communication\Enums\MessageType;

readonly class MessageData
{
    public function __construct(
        public ?string $id,
        public string $school_id,
        public string $sender_id,
        public ?string $subject,
        public string $body,
        public MessageType $type,
        public MessageStatus $status,
        public ?string $sent_at,
    ) {
    }

    public static function create(mixed $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            school_id: $data['school_id'],
            sender_id: (string) ($data['sender_id'] ?? ''),
            subject: $data['subject'] ?? null,
            body: $data['body'],
            type: $data['type'] instanceof MessageType ? $data['type'] : MessageType::from($data['type'] ?? 'direct'),
            status: $data['status'] instanceof MessageStatus ? $data['status'] : MessageStatus::from($data['status'] ?? 'draft'),
            sent_at: $data['sent_at'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'school_id' => $this->school_id,
            'sender_id' => $this->sender_id,
            'subject' => $this->subject,
            'body' => $this->body,
            'type' => $this->type instanceof MessageType ? $this->type->value : $this->type,
            'status' => $this->status instanceof MessageStatus ? $this->status->value : $this->status,
            'sent_at' => $this->sent_at,
        ];
    }
}
