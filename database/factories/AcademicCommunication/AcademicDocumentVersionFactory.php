<?php

declare(strict_types=1);

namespace Database\Factories\AcademicCommunication;

use App\Domains\AcademicCommunication\Domain\Enums\DocumentStatus;
use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocument;
use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocumentVersion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AcademicDocumentVersion>
 */
class AcademicDocumentVersionFactory extends Factory
{
    protected $model = AcademicDocumentVersion::class;

    public function definition(): array
    {
        $content = [
            'body' => fake()->paragraph(),
            'sections' => [
                'identity' => ['student' => fake()->name()],
                'results' => [
                    ['subject' => 'Mathématiques', 'score' => 16.0],
                    ['subject' => 'Français', 'score' => 14.0],
                ],
            ],
        ];

        return [
            'school_id' => null,
            'academic_document_id' => AcademicDocument::factory(),
            'version_number' => 1,
            'status' => DocumentStatus::Draft,
            'content' => $content,
            'content_hash' => AcademicDocumentVersion::hashContent($content),
            'is_published' => false,
            'published_at' => null,
            'created_by' => null,
        ];
    }

    public function forDocument(AcademicDocument $document): static
    {
        return $this->state(fn (): array => [
            'school_id' => $document->school_id,
            'academic_document_id' => $document->getKey(),
        ]);
    }

    public function withNumber(int $versionNumber): static
    {
        return $this->state(fn (): array => ['version_number' => $versionNumber]);
    }

    public function published(): static
    {
        return $this->state(fn (): array => [
            'status' => DocumentStatus::Published,
            'is_published' => true,
            'published_at' => now(),
        ]);
    }
}
