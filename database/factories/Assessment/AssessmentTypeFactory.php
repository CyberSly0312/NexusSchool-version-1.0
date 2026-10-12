<?php

declare(strict_types=1);

namespace Database\Factories\Assessment;

use App\Domains\Assessment\Domain\Enums\AssessmentType;
use App\Domains\Assessment\Infrastructure\Models\AssessmentType as AssessmentTypeModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AssessmentTypeModel>
 */
class AssessmentTypeFactory extends Factory
{
    protected $model = AssessmentTypeModel::class;

    public function definition(): array
    {
        return [
            'school_id' => 'fac-'.(string) \Illuminate\Support\Str::ulid(),
            'name' => fake()->unique()->randomElement([
                'Quiz', 'Devoir maison', 'Devoir', 'Projet', 'Oral',
                'Examen partiel', 'Examen final', 'Évaluation continue',
            ]),
            'code' => 'AT-'.strtoupper(fake()->unique()->lexify('????')),
            'type_key' => fake()->randomElement(AssessmentType::cases()),
            'description' => fake()->sentence(),
            'is_active' => true,
            'sort_order' => fake()->numberBetween(1, 100),
        ];
    }

    public function forSchool(string $schoolId): static
    {
        return $this->state(fn (): array => ['school_id' => $schoolId]);
    }

    public function ofType(AssessmentType $type): static
    {
        return $this->state(fn (): array => [
            'name' => $type->label(),
            'type_key' => $type,
        ]);
    }
}
