<?php

declare(strict_types=1);

namespace App\Domains\Communication\DTOs;

use App\Domains\Communication\Enums\NotificationChannel;
use App\Domains\Communication\Enums\TemplateCategory;

readonly class NotificationTemplateData
{
    public function __construct(
        public ?string $id,
        public string $school_id,
        public string $name,
        public string $slug,
        public TemplateCategory $category,
        public NotificationChannel $channel,
        public ?string $subject,
        public string $body,
        public ?array $variables,
        public bool $is_active,
    ) {
    }

    public static function create(mixed $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            school_id: $data['school_id'],
            name: $data['name'],
            slug: $data['slug'],
            category: TemplateCategory::from($data['category'] ?? 'general'),
            channel: NotificationChannel::from($data['channel'] ?? 'in_app'),
            subject: $data['subject'] ?? null,
            body: $data['body'],
            variables: $data['variables'] ?? null,
            is_active: $data['is_active'] ?? true,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'school_id' => $this->school_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'category' => $this->category->value,
            'channel' => $this->channel->value,
            'subject' => $this->subject,
            'body' => $this->body,
            'variables' => $this->variables,
            'is_active' => $this->is_active,
        ];
    }
}
