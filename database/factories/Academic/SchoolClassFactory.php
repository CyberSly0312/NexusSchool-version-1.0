<?php

declare(strict_types=1);

namespace Database\Factories\Academic;

use App\Domains\Academic\Domain\Enums\EntityStatus;
use App\Domains\Academic\Infrastructure\Models\AcademicProgram;
use App\Domains\Academic\Infrastructure\Models\SchoolClass;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SchoolClass>
 */
class SchoolClassFactory extends Factory
{
    protected $model = SchoolClass::class;

    public function definition(): array
    {
        return [
            'school_id' => null,
            'academic_program_id' => AcademicProgram::factory(),
            'education_level_id' => null,
            'stream_id' => null,
            'name' => fake()->randomElement(['6ème', '5ème', '4ème', '3ème']),
            'code' => fake()->unique()->bothify('CL##'),
            'capacity' => fake()->optional()->numberBetween(20, 60),
            'description' => fake()->optional()->sentence(),
            'status' => EntityStatus::Active,
        ];
    }
}
