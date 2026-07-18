<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Factories\Factory;

class AuditLogFactory extends Factory
{
    protected $model = AuditLog::class;

    public function definition(): array
    {
        return [
            'school_id' => fake()->uuid(),
            'auditable_type' => 'App\\Models\\User',
            'auditable_id' => fake()->uuid(),
            'action' => fake()->randomElement(['created', 'updated', 'deleted']),
            'old_values' => null,
            'new_values' => null,
            'event' => fake()->words(2, true),
            'description' => fake()->sentence(),
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
            'tags' => null,
            'user_id' => null,
            'user_type' => null,
        ];
    }
}
