<?php

namespace Database\Factories;

use App\Models\Notification;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Notification>
 */
class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition(): array
    {
        return [
            'school_id' => '1',
            'sender_id' => null,
            'template_id' => null,
            'type' => $this->faker->randomElement(['system', 'fee_reminder', 'attendance', 'grade', 'library', 'transport']),
            'title' => $this->faker->sentence(6),
            'body' => $this->faker->paragraph(2),
            'data' => null,
            'status' => 'pending',
            'channel' => 'in_app',
            'scheduled_at' => null,
            'sent_at' => null,
            'delivered_at' => null,
            'read_at' => null,
            'retry_count' => 0,
            'max_retries' => 3,
            'last_error' => null,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'pending']);
    }

    public function queued(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'queued']);
    }

    public function sent(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }

    public function delivered(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'delivered',
            'sent_at' => now(),
            'delivered_at' => now(),
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'failed',
            'last_error' => 'Delivery failed',
            'retry_count' => 1,
        ]);
    }

    public function scheduled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'queued',
            'scheduled_at' => now()->addHours(2),
        ]);
    }
}
