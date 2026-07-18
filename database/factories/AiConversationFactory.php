<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\AiConversation;
use Illuminate\Database\Eloquent\Factories\Factory;

class AiConversationFactory extends Factory
{
    protected $model = AiConversation::class;

    public function definition(): array
    {
        return [
            'school_id' => '1',
            'ai_provider_id' => null,
            'user_id' => null,
            'title' => fake()->sentence(4),
            'status' => 'active',
            'context_type' => null,
            'context_id' => null,
            'model' => 'gpt-4o',
            'system_prompt' => null,
            'summary' => null,
            'message_count' => 0,
            'total_input_tokens' => 0,
            'total_output_tokens' => 0,
            'context_window_used' => 0,
            'context_window_limit' => 128000,
            'metadata' => null,
        ];
    }

    public function forSchool(string $schoolId): static
    {
        return $this->state(fn () => ['school_id' => $schoolId]);
    }

    public function archived(): static
    {
        return $this->state(fn () => ['status' => 'archived']);
    }

    public function closed(): static
    {
        return $this->state(fn () => ['status' => 'closed']);
    }

    public function summarized(): static
    {
        return $this->state(fn () => [
            'status' => 'summarized',
            'summary' => fake()->paragraph(),
        ]);
    }
}
