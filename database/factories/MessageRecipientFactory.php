<?php

namespace Database\Factories;

use App\Models\MessageRecipient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MessageRecipient>
 */
class MessageRecipientFactory extends Factory
{
    protected $model = MessageRecipient::class;

    public function definition(): array
    {
        return [
            'school_id' => '1',
            'message_id' => null,
            'recipient_id' => null,
            'status' => 'pending',
            'read_at' => null,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'pending']);
    }

    public function delivered(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'delivered']);
    }

    public function read(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'delivered',
            'read_at' => now(),
        ]);
    }
}
