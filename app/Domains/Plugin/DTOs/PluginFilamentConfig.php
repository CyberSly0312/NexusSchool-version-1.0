<?php
declare(strict_types=1);

namespace App\Domains\Plugin\DTOs;

final readonly class PluginFilamentConfig
{
    public function __construct(
        public array $resources = [],
        public array $pages = [],
        public array $widgets = [],
        public array $navigation = [],
        public ?string $navigationGroup = null,
        public ?string $navigationGroupIcon = null,
        public ?int $navigationGroupSort = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            resources: $data['resources'] ?? [],
            pages: $data['pages'] ?? [],
            widgets: $data['widgets'] ?? [],
            navigation: $data['navigation'] ?? [],
            navigationGroup: $data['navigation_group'] ?? null,
            navigationGroupIcon: $data['navigation_group_icon'] ?? null,
            navigationGroupSort: $data['navigation_group_sort'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'resources' => $this->resources,
            'pages' => $this->pages,
            'widgets' => $this->widgets,
            'navigation' => $this->navigation,
            'navigation_group' => $this->navigationGroup,
            'navigation_group_icon' => $this->navigationGroupIcon,
            'navigation_group_sort' => $this->navigationGroupSort,
        ];
    }
}
