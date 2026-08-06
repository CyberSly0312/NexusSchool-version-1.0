<?php

declare(strict_types=1);

namespace Database\Factories\AcademicCommunication;

use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocument;
use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocumentVersion;
use App\Domains\AcademicCommunication\Infrastructure\Models\GeneratedFile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GeneratedFile>
 */
class GeneratedFileFactory extends Factory
{
    protected $model = GeneratedFile::class;

    public function definition(): array
    {
        $fileName = fake()->unique()->slug() . '.pdf';

        return [
            'school_id' => null,
            'academic_document_id' => AcademicDocument::factory(),
            'document_version_id' => AcademicDocumentVersion::factory(),
            'file_path' => 'academic-documents/' . $fileName,
            'file_name' => $fileName,
            'disk' => 'local',
            'mime_type' => 'application/pdf',
            'size_bytes' => fake()->numberBetween(8_000, 120_000),
            'checksum' => fake()->sha256(),
            'is_current' => true,
        ];
    }

    public function forSchool(string $schoolId): static
    {
        return $this->state(fn (): array => ['school_id' => $schoolId]);
    }

    public function historical(): static
    {
        return $this->state(fn (): array => ['is_current' => false]);
    }
}
