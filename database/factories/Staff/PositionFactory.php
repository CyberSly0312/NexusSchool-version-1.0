<?php

declare(strict_types=1);

namespace Database\Factories\Staff;

use App\Domains\Staff\Domain\Enums\PositionCategory;
use App\Domains\Staff\Infrastructure\Models\Position;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Position>
 */
class PositionFactory extends Factory
{
    protected $model = Position::class;

    public function definition(): array
    {
        return [
            'school_id' => 'fac-'.(string) \Illuminate\Support\Str::ulid(),
            'code' => fake()->unique()->regexify('[a-z_]{4,12}'),
            'title' => fake()->jobTitle(),
            'description' => fake()->optional()->sentence(),
            'category' => fake()->randomElement(PositionCategory::cases()),
            'is_active' => true,
            'sort_order' => 0,
        ];
    }

    public function teaching(): static
    {
        return $this->state(fn (): array => ['category' => PositionCategory::Teaching]);
    }

    public function forSchool(string $schoolId): static
    {
        return $this->state(fn (): array => ['school_id' => $schoolId]);
    }
}
