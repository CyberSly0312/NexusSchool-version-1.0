<?php

namespace Database\Factories;

use App\Domains\Analytics\Enums\ReportFormat;
use App\Domains\Analytics\Enums\ReportStatus;
use App\Models\Report;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Report>
 */
class ReportFactory extends Factory
{
    protected $model = Report::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'school_id' => '1',
            'name' => ucfirst($name),
            'description' => fake()->sentence(),
            'type' => fake()->randomElement(['enrollment', 'attendance', 'finance', 'library', 'transport', 'custom']),
            'format' => fake()->randomElement(ReportFormat::cases()),
            'status' => ReportStatus::Draft,
            'config' => null,
            'scheduled_at' => null,
            'scheduled_frequency' => null,
            'last_generated_at' => null,
            'generated_by' => null,
        ];
    }

    public function forSchool(string $schoolId): static
    {
        return $this->state(fn () => ['school_id' => $schoolId]);
    }

    public function scheduled(): static
    {
        return $this->state(fn () => [
            'status' => ReportStatus::Scheduled,
            'scheduled_at' => now(),
            'scheduled_frequency' => 'weekly',
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn () => [
            'status' => ReportStatus::Draft,
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn () => [
            'status' => ReportStatus::Completed,
            'last_generated_at' => now(),
        ]);
    }
}
