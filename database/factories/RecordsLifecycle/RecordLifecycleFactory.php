<?php

declare(strict_types=1);

namespace Database\Factories\RecordsLifecycle;

use App\Domains\RecordsLifecycle\Domain\Enums\RecordLifecycleState;
use App\Domains\RecordsLifecycle\Infrastructure\Models\RecordLifecycle;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<RecordLifecycle>
 */
class RecordLifecycleFactory extends Factory
{
    protected $model = RecordLifecycle::class;

    public function definition(): array
    {
        return [
            'uuid' => (string) Str::ulid(),
            'school_id' => 'fac-'.(string) Str::ulid(),
            'source_domain' => 'report_card',
            'source_type' => 'report_card',
            'source_id' => (string) $this->faker->randomNumber(5),
            'source_version' => null,
            'lifecycle_state' => RecordLifecycleState::Registered,
            'academic_context' => null,
            'created_by' => null,
            'origin_event' => null,
            'published_at' => null,
            'archived_at' => null,
        ];
    }

    public function forSchool(string $schoolId): static
    {
        return $this->state(fn (): array => ['school_id' => $schoolId]);
    }

    public function forSource(string $domain, string $type, int|string $sourceId): static
    {
        return $this->state(fn (): array => [
            'source_domain' => $domain,
            'source_type' => $type,
            'source_id' => (string) $sourceId,
        ]);
    }

    public function registered(): static
    {
        return $this->state(fn (): array => [
            'lifecycle_state' => RecordLifecycleState::Registered,
        ]);
    }

    public function published(): static
    {
        return $this->state(fn (): array => [
            'lifecycle_state' => RecordLifecycleState::Published,
            'published_at' => now(),
        ]);
    }

    public function archived(): static
    {
        return $this->state(fn (): array => [
            'lifecycle_state' => RecordLifecycleState::Archived,
            'archived_at' => now(),
        ]);
    }
}
