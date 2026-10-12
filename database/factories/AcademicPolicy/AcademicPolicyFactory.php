<?php

declare(strict_types=1);

namespace Database\Factories\AcademicPolicy;

use App\Domains\AcademicPolicy\Domain\Enums\AcademicPolicyScope;
use App\Domains\AcademicPolicy\Domain\Enums\AcademicPolicyValueType;
use App\Domains\AcademicPolicy\Infrastructure\Models\AcademicPolicy;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AcademicPolicy>
 */
class AcademicPolicyFactory extends Factory
{
    protected $model = AcademicPolicy::class;

    public function definition(): array
    {
        return [
            'school_id' => 'fac-'.(string) \Illuminate\Support\Str::ulid(),
            'key' => fake()->unique()->slug(2),
            'label' => fake()->sentence(3),
            'description' => null,
            'value_type' => AcademicPolicyValueType::Float,
            'value' => 50.0,
            'scope' => AcademicPolicyScope::School,
            'academic_year_id' => null,
            'academic_subject_id' => null,
            'is_active' => true,
            'created_by' => null,
        ];
    }

    public function forSchool(string $schoolId): static
    {
        return $this->state(fn (): array => ['school_id' => $schoolId]);
    }

    public function inactive(): static
    {
        return $this->state(fn (): array => ['is_active' => false]);
    }
}
