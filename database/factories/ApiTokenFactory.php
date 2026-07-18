<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ApiClient;
use App\Models\ApiToken;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ApiTokenFactory extends Factory
{
    protected $model = ApiToken::class;

    public function definition(): array
    {
        return [
            'api_client_id' => ApiClient::factory(),
            'school_id' => '1',
            'user_id' => null,
            'name' => fake()->word(),
            'token' => Str::random(80),
            'abilities' => ['*'],
            'expires_at' => null,
            'last_used_at' => null,
            'is_active' => true,
        ];
    }

    public function forSchool(string $schoolId): static
    {
        return $this->state(fn () => ['school_id' => $schoolId]);
    }

    public function expired(): static
    {
        return $this->state(fn () => ['expires_at' => fake()->dateTimeBetween('-30 days', '-1 day')]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
