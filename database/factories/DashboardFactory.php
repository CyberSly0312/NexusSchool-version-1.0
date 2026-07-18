<?php

namespace Database\Factories;

use App\Domains\Analytics\Enums\DashboardType;
use App\Models\Dashboard;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Dashboard>
 */
class DashboardFactory extends Factory
{
    protected $model = Dashboard::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'school_id' => '1',
            'name' => ucfirst($name),
            'description' => fake()->sentence(),
            'type' => fake()->randomElement(DashboardType::cases()),
            'is_default' => fake()->boolean(20),
            'is_active' => true,
            'layout' => null,
            'created_by' => null,
        ];
    }

    public function forSchool(string $schoolId): static
    {
        return $this->state(fn () => ['school_id' => $schoolId]);
    }

    public function academic(): static
    {
        return $this->state(fn () => ['type' => DashboardType::Academic]);
    }

    public function finance(): static
    {
        return $this->state(fn () => ['type' => DashboardType::Finance]);
    }

    public function school(): static
    {
        return $this->state(fn () => ['type' => DashboardType::School]);
    }
}
