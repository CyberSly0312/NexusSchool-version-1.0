<?php
declare(strict_types=1);

namespace App\Domains\Plugin\DTOs;

final readonly class PluginMenuData
{
    public function __construct(
        public string $label,
        public string $url,
        public ?string $icon = null,
        public ?string $permission = null,
        public ?string $group = null,
        public ?int $sort = null,
        public array $children = [],
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            label: $data['label'] ?? '',
            url: $data['url'] ?? '',
            icon: $data['icon'] ?? null,
            permission: $data['permission'] ?? null,
            group: $data['group'] ?? null,
            sort: $data['sort'] ?? null,
            children: array_map(fn(array $child) => self::fromArray($child), $data['children'] ?? []),
        );
    }

    public function toArray(): array
    {
        return [
            'label' => $this->label,
            'url' => $this->url,
            'icon' => $this->icon,
            'permission' => $this->permission,
            'group' => $this->group,
            'sort' => $this->sort,
            'children' => array_map(fn(self $child) => $child->toArray(), $this->children),
        ];
    }
}
