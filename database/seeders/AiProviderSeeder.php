<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\AiProvider;
use App\Domains\School\Models\School;
use Illuminate\Database\Seeder;

class AiProviderSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::where('slug', 'nexus-demo')->first();
        if (! $school) {
            return;
        }

        $providers = [
            [
                'name' => 'OpenAI',
                'slug' => 'openai',
                'driver' => 'openai',
                'description' => 'OpenAI GPT models for AI completions',
                'is_active' => true,
                'supported_models' => ['gpt-4o', 'gpt-4o-mini', 'gpt-4-turbo', 'gpt-3.5-turbo'],
                'default_model' => 'gpt-4o',
                'rate_limit_per_minute' => 60,
                'daily_token_limit' => 1000000,
                'monthly_token_limit' => 30000000,
            ],
            [
                'name' => 'Anthropic',
                'slug' => 'anthropic',
                'driver' => 'anthropic',
                'description' => 'Anthropic Claude models for AI completions',
                'is_active' => false,
                'supported_models' => ['claude-sonnet-4-20250514', 'claude-3-5-haiku-20241022'],
                'default_model' => 'claude-sonnet-4-20250514',
                'rate_limit_per_minute' => 40,
                'daily_token_limit' => 500000,
                'monthly_token_limit' => 15000000,
            ],
            [
                'name' => 'Google Gemini',
                'slug' => 'google-gemini',
                'driver' => 'gemini',
                'description' => 'Google Gemini models for AI completions',
                'is_active' => false,
                'supported_models' => ['gemini-1.5-pro', 'gemini-1.5-flash'],
                'default_model' => 'gemini-1.5-pro',
                'rate_limit_per_minute' => 50,
                'daily_token_limit' => 500000,
                'monthly_token_limit' => 15000000,
            ],
            [
                'name' => 'Ollama (Local)',
                'slug' => 'ollama',
                'driver' => 'ollama',
                'description' => 'Local Ollama instance for AI completions',
                'is_active' => false,
                'supported_models' => ['llama3', 'mistral', 'codellama'],
                'default_model' => 'llama3',
                'rate_limit_per_minute' => 10,
                'daily_token_limit' => null,
                'monthly_token_limit' => null,
            ],
        ];

        foreach ($providers as $provider) {
            AiProvider::firstOrCreate(
                ['school_id' => $school->id, 'slug' => $provider['slug']],
                array_merge($provider, ['school_id' => $school->id])
            );
        }
    }
}
