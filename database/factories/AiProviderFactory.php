<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\AiProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AiProviderFactory extends Factory
{
    protected $model = AiProvider::class;

    public function definition(): array
    {
        $name = fake()->unique()->company().' AI';
        $driver = fake()->randomElement(['openai', 'anthropic', 'gemini', 'azure_openai', 'ollama']);

        return [
            'school_id' => '1',
            'name' => $name,
            'driver' => $driver,
            'description' => fake()->sentence(),
            'is_active' => true,
            'configuration' => null,
            'supported_models' => config("ai.providers.{$driver}.supported_models", ['gpt-4o']),
            'default_model' => config("ai.providers.{$driver}.default_model", 'gpt-4o'),
            'rate_limit_per_minute' => 60,
            'daily_token_limit' => 1000000,
            'monthly_token_limit' => 30000000,
            'created_by' => null,
        ];
    }

    public function forSchool(string $schoolId): static
    {
        return $this->state(fn () => ['school_id' => $schoolId]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }

    public function openai(): static
    {
        return $this->state(fn () => [
            'name' => 'OpenAI',
            'slug' => 'openai',
            'driver' => 'openai',
            'supported_models' => ['gpt-4o', 'gpt-4o-mini', 'gpt-4-turbo'],
            'default_model' => 'gpt-4o',
        ]);
    }

    public function anthropic(): static
    {
        return $this->state(fn () => [
            'name' => 'Anthropic',
            'slug' => 'anthropic',
            'driver' => 'anthropic',
            'supported_models' => ['claude-sonnet-4-20250514', 'claude-3-5-haiku-20241022'],
            'default_model' => 'claude-sonnet-4-20250514',
        ]);
    }
}
