<?php

declare(strict_types=1);

namespace App\Domains\Communication\DTOs;

use App\Domains\Communication\Enums\AnnouncementScope;

readonly class AnnouncementData
{
    public function __construct(
        public ?string $id,
        public string $school_id,
        public string $author_id,
        public string $title,
        public string $body,
        public AnnouncementScope $scope,
        public ?string $target_id,
        public string $priority,
        public bool $is_pinned,
        public string $status,
        public ?string $published_at,
        public ?string $expires_at,
    ) {
    }

    public static function create(mixed $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            school_id: $data['school_id'],
            author_id: (string) ($data['author_id'] ?? ''),
            title: $data['title'],
            body: $data['body'],
            scope: $data['scope'] instanceof AnnouncementScope ? $data['scope'] : AnnouncementScope::from($data['scope'] ?? 'school_wide'),
            target_id: $data['target_id'] ?? null,
            priority: $data['priority'] ?? 'normal',
            is_pinned: $data['is_pinned'] ?? false,
            status: $data['status'] ?? 'draft',
            published_at: $data['published_at'] ?? null,
            expires_at: $data['expires_at'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'school_id' => $this->school_id,
            'author_id' => $this->author_id,
            'title' => $this->title,
            'body' => $this->body,
            'scope' => $this->scope instanceof AnnouncementScope ? $this->scope->value : $this->scope,
            'target_id' => $this->target_id,
            'priority' => $this->priority,
            'is_pinned' => $this->is_pinned,
            'status' => $this->status,
            'published_at' => $this->published_at,
            'expires_at' => $this->expires_at,
        ];
    }
}
