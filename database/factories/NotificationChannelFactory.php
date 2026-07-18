<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\NotificationChannel;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationChannelFactory extends Factory
{
    protected $model = NotificationChannel::class;

    public function definition(): array
    {
        return [
            'school_id' => fake()->uuid(),
            'name' => fake()->word() . ' Channel',
            'type' => fake()->randomElement(['email', 'sms', 'database', 'broadcast']),
            'is_active' => true,
            'config' => null,
            'description' => fake()->sentence(),
        ];
    }
}
