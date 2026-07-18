<?php

namespace Database\Factories;

use App\Models\Message;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Message>
 */
class MessageFactory extends Factory
{
    protected $model = Message::class;

    public function definition(): array
    {
        return [
            'school_id' => '1',
            'sender_id' => null,
            'subject' => $this->faker->sentence(4),
            'body' => $this->faker->paragraph(2),
            'type' => 'direct',
            'status' => 'draft',
            'sent_at' => null,
        ];
    }

    public function direct(): static
    {
        return $this->state(fn (array $attributes) => ['type' => 'direct']);
    }

    public function broadcast(): static
    {
        return $this->state(fn (array $attributes) => ['type' => 'broadcast']);
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'draft']);
    }

    public function sent(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }

    public function archived(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'archived']);
    }
}
