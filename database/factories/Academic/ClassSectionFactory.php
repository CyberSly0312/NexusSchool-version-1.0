<?php

declare(strict_types=1);

namespace Database\Factories\Academic;

use App\Domains\Academic\Domain\Enums\EntityStatus;
use App\Domains\Academic\Infrastructure\Models\ClassSection;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ClassSection>
 */
class ClassSectionFactory extends Factory
{
    protected $model = ClassSection::class;

    public function definition(): array
    {
        return [
            'school_id' => 'fac-'.(string) \Illuminate\Support\Str::ulid(),
            'school_class_id' => null,
            'name' => fake()->randomElement(['A', 'B', 'C']),
            'code' => fake()->unique()->bothify('SEC##'),
            'capacity' => fake()->optional()->numberBetween(15, 40),
            'status' => EntityStatus::Active,
        ];
    }
}
