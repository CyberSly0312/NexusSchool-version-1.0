<?php

declare(strict_types=1);

namespace Database\Factories\Staff;

use App\Domains\Staff\Infrastructure\Models\Certification;
use App\Domains\Staff\Infrastructure\Models\Staff;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Certification>
 */
class CertificationFactory extends Factory
{
    protected $model = Certification::class;

    public function definition(): array
    {
        return [
            'school_id' => null,
            'staff_id' => Staff::factory(),
            'name' => fake()->words(3, true),
            'issuing_authority' => fake()->company(),
            'certification_number' => fake()->optional()->numerify('CERT-####'),
            'issued_at' => fake()->dateTimeBetween('-15 years', 'now')->format('Y-m-d'),
            'expires_at' => fake()->optional()->dateTimeBetween('now', '+5 years')->format('Y-m-d'),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    public function forStaff(int $staffId): static
    {
        return $this->state(fn (): array => ['staff_id' => $staffId]);
    }
}
