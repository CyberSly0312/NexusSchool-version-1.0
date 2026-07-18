<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\SystemSetting;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SystemSettingFactory extends Factory
{
    protected $model = SystemSetting::class;

    public function definition(): array
    {
        $group = fake()->randomElement(['general', 'mail', 'auth', 'features', 'api']);
        $key = Str::slug(fake()->words(2, true));

        return [
            'school_id' => fake()->uuid(),
            'group' => $group,
            'key' => $key,
            'value' => fake()->word(),
            'type' => 'string',
            'is_encrypted' => false,
            'is_readonly' => false,
            'is_system' => false,
            'description' => fake()->sentence(),
            'metadata' => null,
        ];
    }

    public function global(): static
    {
        return $this->state(fn () => ['school_id' => null]);
    }

    public function system(): static
    {
        return $this->state(fn () => ['is_system' => true]);
    }

    public function encrypted(): static
    {
        return $this->state(fn () => ['type' => 'encrypted', 'is_encrypted' => true]);
    }
}
