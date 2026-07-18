<?php

declare(strict_types=1);

namespace App\Domains\Analytics\Services;

use App\Domains\Analytics\DTOs\DashboardData;
use App\Domains\Analytics\DTOs\DashboardWidgetData;
use App\Domains\Analytics\Events\DashboardCreated;
use App\Domains\Analytics\Events\WidgetAdded;
use App\Domains\Analytics\Rules\DashboardRules;
use App\Domains\Analytics\Rules\DashboardWidgetRules;
use App\Models\Dashboard;
use App\Models\DashboardWidget;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class DashboardService
{
    public function __construct(
        protected Dashboard $model = new Dashboard(),
        protected DashboardWidget $widgetModel = new DashboardWidget(),
    ) {
    }

    public function create(array $data): DashboardData
    {
        $validated = DashboardRules::validate($data, 'create');
        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);
        $this->validateUniqueSlugPerSchool($validated['school_id'], $validated['name'], null);

        return DB::transaction(function () use ($validated) {
            $dashboard = $this->model->create($validated);
            DashboardCreated::dispatch($dashboard);

            return DashboardData::create($dashboard->toArray());
        });
    }

    public function update(Dashboard $dashboard, array $data): DashboardData
    {
        $validated = DashboardRules::validate($data, 'update', $dashboard->id);

        if (isset($validated['name'])) {
            $this->validateUniqueSlugPerSchool($dashboard->school_id, $validated['name'], $dashboard->id);
        }

        $dashboard->update($validated);

        return DashboardData::create($dashboard->fresh()->toArray());
    }

    public function delete(Dashboard $dashboard): void
    {
        $dashboard->delete();
    }

    public function findById(string $id): Dashboard
    {
        return $this->model->with('widgets')->findOrFail($id);
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['school_id'])) {
            $query->where('school_id', $filters['school_id']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return $query->orderBy('name')->paginate($perPage);
    }

    public function addWidget(string $dashboardId, array $data): DashboardWidgetData
    {
        $data['dashboard_id'] = $dashboardId;
        $validated = DashboardWidgetRules::validate($data, 'create');
        $this->validateUniquePositionPerDashboard($dashboardId, $validated['position'] ?? 0);

        return DB::transaction(function () use ($validated) {
            $widget = $this->widgetModel->create($validated);
            WidgetAdded::dispatch($widget);

            return DashboardWidgetData::create($widget->toArray());
        });
    }

    public function updateWidget(DashboardWidget $widget, array $data): DashboardWidgetData
    {
        $validated = DashboardWidgetRules::validate($data, 'update', $widget->id);
        $widget->update($validated);

        return DashboardWidgetData::create($widget->fresh()->toArray());
    }

    public function removeWidget(DashboardWidget $widget): void
    {
        $widget->delete();
    }

    private function validateUniqueSlugPerSchool(string $schoolId, string $name, ?string $excludeId): void
    {
        $slug = Str::slug($name);
        $query = $this->model->where('school_id', $schoolId)->where('slug', $slug);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        if ($query->exists()) {
            throw ValidationException::withMessages(['name' => 'A dashboard with this name already exists in this school.']);
        }
    }

    private function validateUniquePositionPerDashboard(string $dashboardId, int $position, ?string $excludeId = null): void
    {
        $query = $this->widgetModel->where('dashboard_id', $dashboardId)->where('position', $position);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        if ($query->exists()) {
            throw ValidationException::withMessages(['position' => 'A widget with this position already exists on this dashboard.']);
        }
    }
}
