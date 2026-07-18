<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\AiKnowledgeSource;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AiKnowledgeSourceFactory extends Factory
{
    protected $model = AiKnowledgeSource::class;

    public function definition(): array
    {
        $name = fake()->words(3, true);

        return [
            'school_id' => '1',
            'name' => ucfirst($name),
            'type' => 'markdown',
            'description' => fake()->sentence(),
            'status' => 'pending',
            'source_path' => null,
            'source_type' => null,
            'content_type' => 'text/markdown',
            'content_size' => null,
            'content_hash' => null,
            'version' => 1,
            'chunk_count' => 0,
            'total_tokens' => 0,
            'configuration' => null,
            'metadata' => null,
            'indexed_at' => null,
            'created_by' => null,
        ];
    }

    public function forSchool(string $schoolId): static
    {
        return $this->state(fn () => ['school_id' => $schoolId]);
    }

    public function indexed(): static
    {
        return $this->state(fn () => [
            'status' => 'indexed',
            'chunk_count' => rand(5, 50),
            'total_tokens' => rand(1000, 10000),
            'indexed_at' => now(),
        ]);
    }

    public function indexing(): static
    {
        return $this->state(fn () => ['status' => 'indexing']);
    }

    public function failed(): static
    {
        return $this->state(fn () => ['status' => 'failed']);
    }
}
