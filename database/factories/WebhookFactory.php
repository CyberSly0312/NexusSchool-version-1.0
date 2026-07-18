<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ApiClient;
use App\Models\Webhook;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class WebhookFactory extends Factory
{
    protected $model = Webhook::class;

    public function definition(): array
    {
        return [
            'api_client_id' => ApiClient::factory(),
            'school_id' => '1',
            'url' => fake()->url(),
            'secret' => Str::random(64),
            'events' => ['student.created'],
            'is_active' => true,
            'description' => null,
            'failure_count' => 0,
            'last_triggered_at' => null,
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

    public function failed(int $count = 3): static
    {
        return $this->state(fn () => [
            'failure_count' => $count,
            'last_triggered_at' => fake()->dateTimeBetween('-7 days', 'now'),
        ]);
    }
}
