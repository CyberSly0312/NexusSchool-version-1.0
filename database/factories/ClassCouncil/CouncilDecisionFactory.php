<?php

declare(strict_types=1);

namespace Database\Factories\ClassCouncil;

use App\Domains\ClassCouncil\Domain\Enums\CouncilDecisionCategory;
use App\Domains\ClassCouncil\Domain\Enums\CouncilDecisionType;
use App\Domains\ClassCouncil\Infrastructure\Models\CouncilDecision;
use App\Domains\ClassCouncil\Infrastructure\Models\CouncilSession;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CouncilDecision>
 */
class CouncilDecisionFactory extends Factory
{
    protected $model = CouncilDecision::class;

    public function definition(): array
    {
        return [
            'school_id' => null,
            'council_session_id' => CouncilSession::factory(),
            'student_id' => null,
            'decision_type' => CouncilDecisionType::Passed,
            'category' => CouncilDecisionCategory::Promotion,
            'code' => 'CC-'.strtoupper(fake()->lexify('????')),
            'label' => CouncilDecisionType::Passed->label(),
            'summary' => null,
            'recommendation' => null,
            'decision_date' => now()->toDateString(),
            'is_override' => false,
            'automatic_decision_type' => null,
            'automatic_decision_label' => null,
            'justification' => null,
            'approved_by' => null,
            'approved_at' => null,
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
