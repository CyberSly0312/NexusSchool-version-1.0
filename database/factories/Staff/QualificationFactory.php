<?php

declare(strict_types=1);

namespace Database\Factories\Staff;

use App\Domains\Staff\Domain\Enums\QualificationLevel;
use App\Domains\Staff\Infrastructure\Models\Qualification;
use App\Domains\Staff\Infrastructure\Models\Staff;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Qualification>
 */
class QualificationFactory extends Factory
{
    protected $model = Qualification::class;

    public function definition(): array
    {
        return [
            'school_id' => null,
            'staff_id' => Staff::factory(),
            'qualification_level' => fake()->randomElement(QualificationLevel::cases()),
            'field_of_study' => fake()->optional()->words(2, true),
            'institution' => fake()->company(),
            'qualification_date' => fake()->dateTimeBetween('-30 years', 'now')->format('Y-m-d'),
            'grade' => fake()->optional()->randomElement(['A', 'B', 'Bien', 'Très bien', 'Mention']),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    public function forStaff(int $staffId): static
    {
        return $this->state(fn (): array => ['staff_id' => $staffId]);
    }
}
