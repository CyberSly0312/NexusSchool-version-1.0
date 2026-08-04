<?php

declare(strict_types=1);

namespace Database\Factories\Guardian;

use App\Domains\Guardian\Domain\Enums\RelationshipCategory;
use App\Domains\Guardian\Infrastructure\Models\RelationshipType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RelationshipType>
 */
class RelationshipTypeFactory extends Factory
{
    protected $model = RelationshipType::class;

    public function definition(): array
    {
        $code = fake()->unique()->slug(2);

        return [
            'school_id' => null,
            'code' => $code,
            'label' => ucfirst(str_replace('-', ' ', $code)),
            'description' => fake()->optional()->sentence(),
            'category' => fake()->randomElement(RelationshipCategory::cases()),
            'is_active' => true,
            'sort_order' => 0,
        ];
    }

    public function active(): static
    {
        return $this->state(fn (): array => ['is_active' => true]);
    }

    public function inactive(): static
    {
        return $this->state(fn (): array => ['is_active' => false]);
    }

    public function category(RelationshipCategory $category): static
    {
        return $this->state(fn (): array => ['category' => $category]);
    }

    public function forSchool(int $schoolId): static
    {
        return $this->state(fn (): array => ['school_id' => $schoolId]);
    }
}
