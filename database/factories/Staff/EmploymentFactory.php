<?php

declare(strict_types=1);

namespace Database\Factories\Staff;

use App\Domains\Staff\Domain\Enums\ContractType;
use App\Domains\Staff\Domain\Enums\EmploymentStatus;
use App\Domains\Staff\Domain\Enums\EmploymentType;
use App\Domains\Staff\Infrastructure\Models\Employment;
use App\Domains\Staff\Infrastructure\Models\Staff;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Employment>
 */
class EmploymentFactory extends Factory
{
    protected $model = Employment::class;

    public function definition(): array
    {
        return [
            'school_id' => null,
            'staff_id' => Staff::factory(),
            'position_id' => null,
            'department_id' => null,
            'employment_type' => EmploymentType::FullTime,
            'contract_type' => ContractType::Permanent,
            'status' => EmploymentStatus::Active,
            'salary_scale' => fake()->optional()->regexify('S[0-9]{2}'),
            'salary_amount' => fake()->optional()->randomFloat(2, 50_000, 1_500_000),
            'contract_start_date' => fake()->dateTimeBetween('-10 years', 'now')->format('Y-m-d'),
            'contract_end_date' => null,
            'termination_date' => null,
            'termination_reason' => null,
            'notes' => fake()->optional()->sentence(),
        ];
    }

    public function terminated(): static
    {
        return $this->state(fn (): array => [
            'status' => EmploymentStatus::Terminated,
            'termination_date' => fake()->dateTimeBetween('-5 years', 'now')->format('Y-m-d'),
            'termination_reason' => fake()->optional()->sentence(),
        ]);
    }

    public function forStaff(int $staffId): static
    {
        return $this->state(fn (): array => ['staff_id' => $staffId]);
    }

    public function forSchool(string $schoolId): static
    {
        return $this->state(fn (): array => ['school_id' => $schoolId]);
    }
}
