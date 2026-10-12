<?php

declare(strict_types=1);

namespace Database\Factories\AcademicCommunication;

use App\Domains\AcademicCommunication\Domain\Enums\ApprovalStatus;use App\Domains\AcademicCommunication\Domain\Enums\DocumentOrigin;
use App\Domains\AcademicCommunication\Domain\Enums\DocumentStatus;
use App\Domains\AcademicCommunication\Domain\Enums\DocumentType;
use App\Domains\AcademicCommunication\Domain\ValueObjects\DocumentNumber;
use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocument;
use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocumentTemplate;
use App\Domains\AcademicCommunication\Infrastructure\Models\PublicationPolicy;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AcademicDocument>
 */
class AcademicDocumentFactory extends Factory
{
    protected $model = AcademicDocument::class;

    public function definition(): array
    {
        $type = DocumentType::ReportCard;

        return [
            'school_id' => 'fac-'.(string) \Illuminate\Support\Str::ulid(),
            'document_number' => DocumentNumber::fromParts(
                'NS',
                '2027',
                'SCH001',
                $type,
                fake()->numberBetween(1, 999999),
            )->toString(),
            'document_type' => $type,
            'title' => fake()->unique()->words(5, true),
            'status' => DocumentStatus::Draft,
            'approval_status' => ApprovalStatus::NotRequired,
            'approval_required' => false,
            'origin' => DocumentOrigin::Manual,
            'academic_calendar_year_id' => null,
            'academic_calendar_term_id' => null,
            'school_class_id' => null,
            'class_section_id' => null,
            'student_id' => null,
            'staff_id' => null,
            'council_session_id' => null,
            'academic_document_template_id' => AcademicDocumentTemplate::factory(),
            'publication_policy_id' => PublicationPolicy::factory(),
            'content' => [],
            'content_version' => 1,
            'visibility_policy' => null,
            'generated_by' => null,
            'generated_at' => null,
            'reviewed_at' => null,
            'published_at' => null,
            'archived_at' => null,
            'metadata' => [],
        ];
    }

    public function forSchool(string $schoolId): static
    {
        return $this->state(fn (): array => ['school_id' => $schoolId]);
    }

    public function ofType(DocumentType $type): static
    {
        return $this->state(fn (): array => [
            'document_type' => $type,
            'document_number' => DocumentNumber::fromParts(
                'NS',
                '2027',
                'SCH001',
                $type,
                fake()->numberBetween(1, 999999),
            )->toString(),
        ]);
    }

    public function generated(): static
    {
        return $this->state(fn (): array => [
            'status' => DocumentStatus::Generated,
            'generated_at' => now(),
        ]);
    }

    public function approved(): static
    {
        return $this->state(fn (): array => [
            'status' => DocumentStatus::Approved,
            'approval_status' => ApprovalStatus::Approved,
            'generated_at' => now()->subDay(),
        ]);
    }

    public function published(): static
    {
        return $this->state(fn (): array => [
            'status' => DocumentStatus::Published,
            'approval_status' => ApprovalStatus::NotRequired,
            'generated_at' => now()->subWeek(),
            'published_at' => now(),
        ]);
    }

    public function archived(): static
    {
        return $this->state(fn (): array => [
            'status' => DocumentStatus::Archived,
            'generated_at' => now()->subMonth(),
            'published_at' => now()->subWeek(),
            'archived_at' => now(),
        ]);
    }
}
