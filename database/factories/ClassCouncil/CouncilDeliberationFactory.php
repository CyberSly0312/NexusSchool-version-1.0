<?php

declare(strict_types=1);

namespace Database\Factories\ClassCouncil;

use App\Domains\ClassCouncil\Infrastructure\Models\CouncilDeliberation;
use App\Domains\ClassCouncil\Infrastructure\Models\CouncilSession;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CouncilDeliberation>
 */
class CouncilDeliberationFactory extends Factory
{
    protected $model = CouncilDeliberation::class;

    public function definition(): array
    {
        return [
            'school_id' => null,
            'council_session_id' => CouncilSession::factory(),
            'student_id' => null,
            'council_decision_id' => null,
            'member' => null,
            'topic' => 'Général',
            'summary' => fake()->sentence(),
            'noted_at' => now(),
            'recorded_by' => null,
        ];
    }

    public function forSchool(string $schoolId): static
    {
        return $this->state(fn (): array => ['school_id' => $schoolId]);
    }

    public function forSession(int|string $sessionId): static
    {
        return $this->state(fn (): array => ['council_session_id' => $sessionId]);
    }
}
