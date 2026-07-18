<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\AiMessage;
use Illuminate\Database\Eloquent\Factories\Factory;

class AiMessageFactory extends Factory
{
    protected $model = AiMessage::class;

    public function definition(): array
    {
        return [
            'school_id' => '1',
            'ai_conversation_id' => null,
            'role' => 'user',
            'content' => fake()->paragraph(),
            'input_tokens' => 0,
            'output_tokens' => 0,
            'model' => 'gpt-4o',
            'finish_reason' => null,
            'metadata' => null,
            'is_archived' => false,
        ];
    }

    public function user(): static
    {
        return $this->state(fn () => ['role' => 'user']);
    }

    public function assistant(): static
    {
        return $this->state(fn () => [
            'role' => 'assistant',
            'output_tokens' => rand(50, 500),
        ]);
    }

    public function system(): static
    {
        return $this->state(fn () => ['role' => 'system']);
    }

    public function archived(): static
    {
        return $this->state(fn () => ['is_archived' => true]);
    }
}
