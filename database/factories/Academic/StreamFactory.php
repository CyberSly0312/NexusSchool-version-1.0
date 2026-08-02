<?php

declare(strict_types=1);

namespace Database\Factories\Academic;

use App\Domains\Academic\Domain\Enums\EntityStatus;
use App\Domains\Academic\Infrastructure\Models\Stream;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Stream>
 */
class StreamFactory extends Factory
{
    protected $model = Stream::class;

    public function definition(): array
    {
        return [
            'school_id' => null,
            'academic_program_id' => null,
            'education_level_id' => null,
            'name' => fake()->randomElement(['Sciences', 'Lettres', 'Technique']),
            'code' => fake()->unique()->bothify('ST##'),
            'description' => fake()->optional()->sentence(),
            'status' => EntityStatus::Active,
        ];
    }
}
