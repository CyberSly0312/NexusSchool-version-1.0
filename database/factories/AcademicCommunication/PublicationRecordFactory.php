<?php

declare(strict_types=1);

namespace Database\Factories\AcademicCommunication;

use App\Domains\AcademicCommunication\Domain\Enums\DocumentChannel;
use App\Domains\AcademicCommunication\Domain\Enums\PublicationStatus;
use App\Domains\AcademicCommunication\Domain\Enums\VisibilityScope;
use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocument;
use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocumentVersion;
use App\Domains\AcademicCommunication\Infrastructure\Models\PublicationRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PublicationRecord>
 */
class PublicationRecordFactory extends Factory
{
    protected $model = PublicationRecord::class;

    public function definition(): array
    {
        return [
            'school_id' => 'fac-'.(string) \Illuminate\Support\Str::ulid(),
            'academic_document_id' => AcademicDocument::factory(),
            'document_version_id' => AcademicDocumentVersion::factory(),
            'channel' => DocumentChannel::InternalPortal,
            'recipient_type' => 'App\\Models\\User',
            'recipient_id' => null,
            'recipient_label' => fake()->name(),
            'visibility_scope' => VisibilityScope::Director,
            'status' => PublicationStatus::Queued,
            'attempts' => 0,
            'last_error' => null,
            'published_at' => null,
            'cancelled_at' => null,
            'metadata' => [],
        ];
    }

    public function forSchool(string $schoolId): static
    {
        return $this->state(fn (): array => ['school_id' => $schoolId]);
    }

    public function completed(): static
    {
        return $this->state(fn (): array => [
            'status' => PublicationStatus::Completed,
            'attempts' => 1,
            'published_at' => now(),
        ]);
    }

    public function failed(string $error): static
    {
        return $this->state(fn (): array => [
            'status' => PublicationStatus::Failed,
            'attempts' => 1,
            'last_error' => $error,
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (): array => [
            'status' => PublicationStatus::Cancelled,
            'cancelled_at' => now(),
        ]);
    }
}
