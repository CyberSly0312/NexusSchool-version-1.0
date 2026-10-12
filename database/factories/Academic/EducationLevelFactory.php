<?php

declare(strict_types=1);

namespace Database\Factories\Academic;

use App\Domains\Academic\Domain\Enums\EntityStatus;
use App\Domains\Academic\Infrastructure\Models\EducationLevel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EducationLevel>
 */
class EducationLevelFactory extends Factory
{
    protected $model = EducationLevel::class;

    public function definition(): array
    {
        return [
            'school_id' => 'fac-'.(string) \Illuminate\Support\Str::ulid(),
            'name' => fake()->randomElement(['Primaire', 'Collège', 'Lycée']),
            'code' => fake()->unique()->bothify('EL##'),
            'sort_order' => fake()->numberBetween(0, 10),
            'description' => fake()->optional()->sentence(),
            'status' => EntityStatus::Active,
        ];
    }
}
