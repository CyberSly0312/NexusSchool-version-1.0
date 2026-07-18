<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\AiUsageLog;
use Illuminate\Database\Eloquent\Factories\Factory;

class AiUsageLogFactory extends Factory
{
    protected $model = AiUsageLog::class;

    public function definition(): array
    {
        $inputTokens = rand(100, 2000);
        $outputTokens = rand(50, 1000);

        return [
            'school_id' => '1',
            'ai_provider_id' => null,
            'ai_conversation_id' => null,
            'user_id' => null,
            'action' => 'message_generated',
            'model' => 'gpt-4o',
            'input_tokens' => $inputTokens,
            'output_tokens' => $outputTokens,
            'total_tokens' => $inputTokens + $outputTokens,
            'estimated_cost' => round(($inputTokens * 0.00001) + ($outputTokens * 0.00003), 6),
            'latency_ms' => rand(200, 3000),
            'was_cached' => false,
            'is_billable' => true,
            'status' => 'success',
            'error_message' => null,
            'metadata' => null,
        ];
    }

    public function forSchool(string $schoolId): static
    {
        return $this->state(fn () => ['school_id' => $schoolId]);
    }

    public function failed(): static
    {
        return $this->state(fn () => [
            'status' => 'failed',
            'error_message' => 'API request failed',
        ]);
    }

    public function cached(): static
    {
        return $this->state(fn () => [
            'was_cached' => true,
            'status' => 'cached',
        ]);
    }
}
