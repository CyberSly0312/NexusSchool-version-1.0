<?php

namespace Database\Factories;

use App\Domains\Analytics\Enums\WidgetType;
use App\Models\DashboardWidget;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DashboardWidget>
 */
class DashboardWidgetFactory extends Factory
{
    protected $model = DashboardWidget::class;

    public function definition(): array
    {
        $title = fake()->unique()->words(3, true);

        return [
            'school_id' => '1',
            'dashboard_id' => \App\Models\Dashboard::factory(),
            'title' => ucfirst($title),
            'widget_type' => fake()->randomElement(WidgetType::cases()),
            'data_source' => fake()->randomElement([
                'enrollment_stats', 'attendance_stats', 'library_stats',
                'transport_stats', 'finance_stats',
            ]),
            'config' => null,
            'position' => fake()->numberBetween(0, 10),
            'is_active' => true,
        ];
    }

    public function forSchool(string $schoolId): static
    {
        return $this->state(fn () => ['school_id' => $schoolId]);
    }

    public function forDashboard(string $dashboardId): static
    {
        return $this->state(fn () => ['dashboard_id' => $dashboardId]);
    }

    public function chart(): static
    {
        return $this->state(fn () => ['widget_type' => WidgetType::Chart]);
    }

    public function metric(): static
    {
        return $this->state(fn () => ['widget_type' => WidgetType::Metric]);
    }
}
