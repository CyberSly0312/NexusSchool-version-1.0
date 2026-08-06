<?php

declare(strict_types=1);

namespace Database\Factories\AcademicCommunication;

use App\Domains\AcademicCommunication\Domain\Enums\DocumentChannel;
use App\Domains\AcademicCommunication\Infrastructure\Models\PublicationChannel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PublicationChannel>
 */
class PublicationChannelFactory extends Factory
{
    protected $model = PublicationChannel::class;

    public function definition(): array
    {
        return [
            'school_id' => null,
            'key' => DocumentChannel::InternalPortal,
            'label' => null,
            'is_enabled' => true,
            'config' => [],
        ];
    }

    public function forSchool(string $schoolId): static
    {
        return $this->state(fn (): array => ['school_id' => $schoolId]);
    }

    public function forChannel(DocumentChannel $channel): static
    {
        return $this->state(fn (): array => ['key' => $channel]);
    }

    public function disabled(): static
    {
        return $this->state(fn (): array => ['is_enabled' => false]);
    }
}
