<?php

namespace Database\Factories;

use App\Models\Announcement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Announcement>
 */
class AnnouncementFactory extends Factory
{
    protected $model = Announcement::class;

    public function definition(): array
    {
        return [
            'school_id' => '1',
            'author_id' => null,
            'title' => $this->faker->sentence(6),
            'body' => $this->faker->paragraph(3),
            'scope' => 'school_wide',
            'target_id' => null,
            'priority' => $this->faker->randomElement(['low', 'normal', 'high', 'urgent']),
            'is_pinned' => false,
            'status' => 'draft',
            'published_at' => null,
            'expires_at' => null,
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'draft']);
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'published',
            'published_at' => now(),
        ]);
    }

    public function archived(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'archived']);
    }

    public function pinned(): static
    {
        return $this->state(fn (array $attributes) => ['is_pinned' => true]);
    }

    public function urgent(): static
    {
        return $this->state(fn (array $attributes) => ['priority' => 'urgent']);
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => now()->subDay(),
        ]);
    }
}
