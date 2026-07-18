<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\MaintenanceWindow;
use Illuminate\Database\Eloquent\Factories\Factory;

class MaintenanceWindowFactory extends Factory
{
    protected $model = MaintenanceWindow::class;

    public function definition(): array
    {
        return [
            'school_id' => fake()->uuid(),
            'name' => 'Maintenance Window ' . fake()->date('Y-m-d'),
            'description' => fake()->sentence(),
            'type' => 'scheduled',
            'status' => 'pending',
            'is_readonly' => false,
            'starts_at' => now()->addDay(),
            'ends_at' => now()->addDay()->addHours(4),
            'actual_start_at' => null,
            'actual_end_at' => null,
            'affected_services' => ['cache', 'queue'],
            'metadata' => null,
            'created_by' => null,
        ];
    }

    public function active(): static
    {
        return $this->state(fn () => [
            'status' => 'active',
            'starts_at' => now()->subHour(),
            'ends_at' => now()->addHour(),
            'actual_start_at' => now()->subHour(),
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn () => [
            'status' => 'completed',
            'starts_at' => now()->subDays(2),
            'ends_at' => now()->subDays(2)->addHours(4),
            'actual_start_at' => now()->subDays(2),
            'actual_end_at' => now()->subDays(2)->addHours(4),
        ]);
    }
}
