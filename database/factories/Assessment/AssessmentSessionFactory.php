<?php

declare(strict_types=1);

namespace Database\Factories\Assessment;

use App\Domains\Assessment\Domain\Enums\AssessmentSessionStatus;
use App\Domains\Assessment\Infrastructure\Models\AssessmentSession;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AssessmentSession>
 */
class AssessmentSessionFactory extends Factory
{
    protected $model = AssessmentSession::class;

    public function definition(): array
    {
        return [
            'school_id' => 'fac-'.(string) \Illuminate\Support\Str::ulid(),
            'assessment_id' => null,
            'title' => fake()->sentence(3),
            'date' => fake()->dateTimeBetween('-7 days', '+14 days')->format('Y-m-d'),
            'starts_at' => '08:00:00',
            'ends_at' => '10:00:00',
            'room_id' => null,
            'staff_id' => null,
            'status' => AssessmentSessionStatus::Scheduled,
        ];
    }

    public function forSchool(string $schoolId): static
    {
        return $this->state(fn (): array => ['school_id' => $schoolId]);
    }

    public function completed(): static
    {
        return $this->state(fn (): array => ['status' => AssessmentSessionStatus::Completed]);
    }
}
