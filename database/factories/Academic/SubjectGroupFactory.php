<?php

declare(strict_types=1);

namespace Database\Factories\Academic;

use App\Domains\Academic\Domain\Enums\EntityStatus;
use App\Domains\Academic\Infrastructure\Models\SubjectGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SubjectGroup>
 */
class SubjectGroupFactory extends Factory
{
    protected $model = SubjectGroup::class;

    public function definition(): array
    {
        return [
            'school_id' => null,
            'name' => fake()->randomElement(['Langues', 'Sciences', 'Mathématiques', 'Arts']),
            'code' => fake()->unique()->bothify('SG##'),
            'sort_order' => fake()->numberBetween(0, 10),
            'description' => fake()->optional()->sentence(),
            'status' => EntityStatus::Active,
        ];
    }
}
