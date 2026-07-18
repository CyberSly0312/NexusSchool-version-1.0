<?php

namespace Database\Factories;

use App\Models\CommunicationChannel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CommunicationChannel>
 */
class CommunicationChannelFactory extends Factory
{
    protected $model = CommunicationChannel::class;

    public function definition(): array
    {
        return [
            'school_id' => '1',
            'name' => $this->faker->randomElement(['School Email', 'SMS Gateway', 'WhatsApp Business', 'Push Service', 'In-App Notifications']),
            'channel_type' => $this->faker->randomElement(['email', 'sms', 'whatsapp', 'push', 'in_app']),
            'is_active' => true,
            'config' => null,
            'from_address' => null,
            'from_name' => null,
        ];
    }

    public function email(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'School Email',
            'channel_type' => 'email',
            'from_address' => 'noreply@nexus-demo.com',
            'from_name' => 'Nexus School',
        ]);
    }

    public function sms(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'SMS Gateway',
            'channel_type' => 'sms',
            'from_name' => 'NEXUS',
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => ['is_active' => false]);
    }
}
