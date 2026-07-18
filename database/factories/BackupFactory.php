<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Backup;
use Illuminate\Database\Eloquent\Factories\Factory;

class BackupFactory extends Factory
{
    protected $model = Backup::class;

    public function definition(): array
    {
        return [
            'school_id' => fake()->uuid(),
            'name' => 'Backup ' . fake()->date('Y-m-d'),
            'type' => 'manual',
            'status' => 'completed',
            'disk' => 'local',
            'path' => storage_path('app/backups/test.sql'),
            'size_bytes' => fake()->numberBetween(1000, 10000000),
            'checksum' => hash('sha256', fake()->word()),
            'tables_included' => null,
            'notes' => fake()->sentence(),
            'error' => null,
            'created_by' => null,
            'started_at' => now()->subMinutes(5),
            'completed_at' => now(),
            'duration_ms' => fake()->numberBetween(1000, 60000),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => ['status' => 'pending']);
    }

    public function running(): static
    {
        return $this->state(fn () => ['status' => 'running', 'started_at' => now()]);
    }

    public function failed(): static
    {
        return $this->state(fn () => ['status' => 'failed', 'error' => 'Test error']);
    }
}
