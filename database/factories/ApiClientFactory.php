<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ApiClient;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ApiClientFactory extends Factory
{
    protected $model = ApiClient::class;

    public function definition(): array
    {
        $name = fake()->company();

        return [
            'school_id' => '1',
            'name' => $name,
            'description' => fake()->sentence(),
            'secret' => Str::random(64),
            'callback_url' => fake()->url(),
            'is_active' => true,
            'scopes' => null,
            'rate_limit_per_minute' => 60,
            'rate_limit_per_day' => 10000,
            'created_by' => null,
        ];
    }

    public function forSchool(string $schoolId): static
    {
        return $this->state(fn () => ['school_id' => $schoolId]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
