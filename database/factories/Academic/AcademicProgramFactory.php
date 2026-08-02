<?php

declare(strict_types=1);

namespace Database\Factories\Academic;

use App\Domains\Academic\Domain\Enums\EntityStatus;
use App\Domains\Academic\Infrastructure\Models\AcademicProgram;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AcademicProgram>
 */
class AcademicProgramFactory extends Factory
{
    protected $model = AcademicProgram::class;

    public function definition(): array
    {
        return [
            'school_id' => null,
            'education_level_id' => null,
            'name' => fake()->randomElement(['Général', 'Technique', 'Professionnel']),
            'code' => fake()->unique()->bothify('PRG##'),
            'description' => fake()->optional()->sentence(),
            'status' => EntityStatus::Active,
        ];
    }
}
