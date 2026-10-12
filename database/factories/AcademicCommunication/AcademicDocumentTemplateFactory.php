<?php

declare(strict_types=1);

namespace Database\Factories\AcademicCommunication;

use App\Domains\AcademicCommunication\Domain\Enums\DocumentType;
use App\Domains\AcademicCommunication\Domain\Enums\SignatureType;
use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocumentTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AcademicDocumentTemplate>
 */
class AcademicDocumentTemplateFactory extends Factory
{
    protected $model = AcademicDocumentTemplate::class;

    public function definition(): array
    {
        return [
            'school_id' => 'fac-'.(string) \Illuminate\Support\Str::ulid(),
            'name' => fake()->unique()->words(3, true),
            'code' => fake()->unique()->lexify('TMP-????-????'),
            'document_type' => DocumentType::ReportCard,
            'content' => [
                'header' => 'Republic of Cameroon, Ministry of Secondary Education',
                'body' => '<p>Document généré automatiquement par le moteur de communication académique.</p>',
                'footer' => 'Ce document est officiel et vérifiable.',
            ],
            'header_html' => '<div class="header">NexusSchool</div>',
            'footer_html' => '<div class="footer">NexusSchool — Moteur de communication académique</div>',
            'branding' => [
                'logo_url' => null,
                'primary_color' => '#0F766E',
                'accent_color' => '#D97706',
            ],
            'typography' => [
                'heading_font' => 'sans-serif',
                'body_font' => 'serif',
            ],
            'signature_areas' => [
                [
                    'role' => 'principal',
                    'label' => 'Le Principal',
                    'type' => SignatureType::Electronic->value,
                    'qr_code' => true,
                ],
            ],
            'qr_code_enabled' => true,
            'verification_code_enabled' => true,
            'locale' => 'fr',
            'is_active' => true,
            'is_default' => true,
            'settings' => [],
        ];
    }

    public function forSchool(string $schoolId): static
    {
        return $this->state(fn (): array => ['school_id' => $schoolId]);
    }

    public function ofType(DocumentType $type): static
    {
        return $this->state(fn (): array => ['document_type' => $type]);
    }

    public function inactive(): static
    {
        return $this->state(fn (): array => [
            'is_active' => false,
            'is_default' => false,
        ]);
    }
}
