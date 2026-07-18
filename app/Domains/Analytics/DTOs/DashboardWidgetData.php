<?php

declare(strict_types=1);

namespace App\Domains\Analytics\DTOs;

use App\Domains\Analytics\Enums\WidgetType;

readonly class DashboardWidgetData
{
    public function __construct(
        public ?string $id,
        public string $schoolId,
        public string $dashboardId,
        public string $title,
        public string $widgetType = 'metric',
        public string $dataSource = '',
        public ?array $config = null,
        public int $position = 0,
        public bool $isActive = true,
    ) {
    }

    public static function create(array $data): self
    {
        $widgetType = $data['widget_type'] ?? 'metric';
        if ($widgetType instanceof WidgetType) {
            $widgetType = $widgetType->value;
        }

        return new self(
            id: $data['id'] ?? null,
            schoolId: $data['school_id'],
            dashboardId: $data['dashboard_id'],
            title: $data['title'],
            widgetType: $widgetType,
            dataSource: $data['data_source'] ?? '',
            config: $data['config'] ?? null,
            position: $data['position'] ?? 0,
            isActive: $data['is_active'] ?? true,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'school_id' => $this->schoolId,
            'dashboard_id' => $this->dashboardId,
            'title' => $this->title,
            'widget_type' => $this->widgetType instanceof WidgetType
                ? $this->widgetType->value : $this->widgetType,
            'data_source' => $this->dataSource,
            'config' => $this->config,
            'position' => $this->position,
            'is_active' => $this->isActive,
        ];
    }
}
