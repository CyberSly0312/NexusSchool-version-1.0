<?php

declare(strict_types=1);

namespace Database\Factories\AcademicCommunication;

use App\Domains\AcademicCommunication\Domain\Enums\VisibilityScope;
use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocument;
use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocumentVersion;
use App\Domains\AcademicCommunication\Infrastructure\Models\DocumentRecipient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DocumentRecipient>
 */
class DocumentRecipientFactory extends Factory
{
    protected $model = DocumentRecipient::class;

    public function definition(): array
    {
        return [
            'school_id' => 'fac-'.(string) \Illuminate\Support\Str::ulid(),
            'academic_document_id' => AcademicDocument::factory(),
            'document_version_id' => AcademicDocumentVersion::factory(),
            'recipient_type' => 'App\\Models\\User',
            'recipient_id' => null,
            'recipient_label' => fake()->name(),
            'visibility_scope' => VisibilityScope::Parent,
            'portal_viewed_at' => null,
            'portal_view_count' => 0,
            'delivered_at' => null,
        ];
    }

    public function forSchool(string $schoolId): static
    {
        return $this->state(fn (): array => ['school_id' => $schoolId]);
    }

    public function viewed(): static
    {
        return $this->state(fn (): array => [
            'portal_viewed_at' => now(),
            'portal_view_count' => 3,
        ]);
    }
}
