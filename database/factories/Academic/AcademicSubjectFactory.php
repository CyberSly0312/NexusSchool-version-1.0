<?php

declare(strict_types=1);

namespace Database\Factories\Academic;

use App\Domains\Academic\Domain\Enums\EntityStatus;
use App\Domains\Academic\Infrastructure\Models\AcademicSubject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AcademicSubject>
 */
class AcademicSubjectFactory extends Factory
{
    protected $model = AcademicSubject::class;

    protected static int $sequence = 0;

    public function definition(): array
    {
        return [
            'school_id' => null,
            'subject_group_id' => null,
            'name' => fake()->randomElement(['Mathématiques', 'Français', 'Physique-Chimie', 'Anglais']).' '.str_pad((string) (self::$sequence++ % 1000), 3, '0', STR_PAD_LEFT),
            'code' => fake()->unique()->bothify('SUB##'),
            'abbreviation' => fake()->optional()->bothify('??'),
            'description' => fake()->optional()->sentence(),
            'status' => EntityStatus::Active,
        ];
    }
}
