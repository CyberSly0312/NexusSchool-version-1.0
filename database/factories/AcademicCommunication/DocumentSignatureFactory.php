<?php

declare(strict_types=1);

namespace Database\Factories\AcademicCommunication;

use App\Domains\AcademicCommunication\Domain\Enums\SignatureType;
use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocument;
use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocumentVersion;
use App\Domains\AcademicCommunication\Infrastructure\Models\DocumentSignature;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DocumentSignature>
 */
class DocumentSignatureFactory extends Factory
{
    protected $model = DocumentSignature::class;

    public function definition(): array
    {
        return [
            'school_id' => 'fac-'.(string) \Illuminate\Support\Str::ulid(),
            'academic_document_id' => AcademicDocument::factory(),
            'document_version_id' => AcademicDocumentVersion::factory(),
            'signature_type' => SignatureType::Electronic,
            'role' => 'principal',
            'signatory_name' => fake()->name(),
            'signatory_user_id' => null,
            'signed_at' => now(),
            'signature_value' => fake()->sha256(),
        ];
    }

    public function forSchool(string $schoolId): static
    {
        return $this->state(fn (): array => ['school_id' => $schoolId]);
    }

    public function manual(): static
    {
        return $this->state(fn (): array => [
            'signature_type' => SignatureType::Manual,
            'signature_value' => null,
        ]);
    }
}
