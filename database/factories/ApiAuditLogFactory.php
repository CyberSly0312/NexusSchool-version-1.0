<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ApiAuditLog;
use Illuminate\Database\Eloquent\Factories\Factory;

class ApiAuditLogFactory extends Factory
{
    protected $model = ApiAuditLog::class;

    public function definition(): array
    {
        return [
            'api_client_id' => null,
            'school_id' => '1',
            'user_id' => null,
            'api_token_id' => null,
            'method' => 'GET',
            'path' => 'api/v1/students',
            'status_code' => 200,
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
            'request_body' => [],
            'response_time_ms' => fake()->numberBetween(50, 500),
        ];
    }

    public function forSchool(string $schoolId): static
    {
        return $this->state(fn () => ['school_id' => $schoolId]);
    }

    public function error(): static
    {
        return $this->state(fn () => [
            'status_code' => fake()->randomElement([400, 401, 403, 404, 500, 502]),
        ]);
    }

    public function slow(): static
    {
        return $this->state(fn () => [
            'response_time_ms' => fake()->numberBetween(1000, 5000),
        ]);
    }
}
