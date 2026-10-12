<?php

declare(strict_types=1);

namespace Database\Factories\Academic;

use App\Domains\Academic\Infrastructure\Models\Coefficient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Coefficient>
 */
class CoefficientFactory extends Factory
{
    protected $model = Coefficient::class;

    public function definition(): array
    {
        return [
            'school_id' => 'fac-'.(string) \Illuminate\Support\Str::ulid(),
            'school_class_id' => null,
            'academic_subject_id' => null,
            'coefficient' => fake()->randomFloat(2, 1, 6),
            'is_optional' => fake()->boolean(20),
            'description' => fake()->optional()->sentence(),
        ];
    }
}
