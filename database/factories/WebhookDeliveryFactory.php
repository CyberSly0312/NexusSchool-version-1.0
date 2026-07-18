<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Webhook;
use App\Models\WebhookDelivery;
use Illuminate\Database\Eloquent\Factories\Factory;

class WebhookDeliveryFactory extends Factory
{
    protected $model = WebhookDelivery::class;

    public function definition(): array
    {
        return [
            'webhook_id' => Webhook::factory(),
            'school_id' => '1',
            'event' => 'student.created',
            'payload' => [],
            'response_status' => 200,
            'response_body' => '{}',
            'success' => true,
            'delivered_at' => now(),
            'retry_count' => 0,
            'max_retries' => 3,
            'next_retry_at' => null,
        ];
    }

    public function forSchool(string $schoolId): static
    {
        return $this->state(fn () => ['school_id' => $schoolId]);
    }

    public function failed(): static
    {
        return $this->state(fn () => [
            'success' => false,
            'response_status' => fake()->randomElement([400, 404, 500, 502, 503]),
            'response_body' => json_encode(['error' => 'Internal Server Error']),
        ]);
    }

    public function pendingRetry(): static
    {
        return $this->state(fn () => [
            'success' => false,
            'retry_count' => fake()->numberBetween(1, 2),
            'next_retry_at' => fake()->dateTimeBetween('now', '+1 hour'),
        ]);
    }
}
