<?php

namespace Database\Factories;

use App\Models\NotificationDelivery;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<NotificationDelivery>
 */
class NotificationDeliveryFactory extends Factory
{
    protected $model = NotificationDelivery::class;

    public function definition(): array
    {
        return [
            'school_id' => '1',
            'notification_id' => null,
            'recipient_id' => null,
            'channel' => 'in_app',
            'status' => 'pending',
            'sent_at' => null,
            'delivered_at' => null,
            'failed_at' => null,
            'error_message' => null,
            'metadata' => null,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'pending']);
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
            'failed_at' => now(),
            'error_message' => 'Delivery failed',
        ]);
    }
}
