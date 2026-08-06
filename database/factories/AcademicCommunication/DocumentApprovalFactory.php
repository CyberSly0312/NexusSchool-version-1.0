<?php

declare(strict_types=1);

namespace Database\Factories\AcademicCommunication;

use App\Domains\AcademicCommunication\Domain\Enums\ApprovalStatus;
use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocument;
use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocumentVersion;
use App\Domains\AcademicCommunication\Infrastructure\Models\DocumentApproval;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DocumentApproval>
 */
class DocumentApprovalFactory extends Factory
{
    protected $model = DocumentApproval::class;

    public function definition(): array
    {
        return [
            'school_id' => null,
            'academic_document_id' => AcademicDocument::factory(),
            'document_version_id' => AcademicDocumentVersion::factory(),
            'approver_role' => 'principal',
            'approver_user_id' => null,
            'status' => ApprovalStatus::Pending,
            'comment' => null,
            'decided_at' => null,
        ];
    }

    public function forSchool(string $schoolId): static
    {
        return $this->state(fn (): array => ['school_id' => $schoolId]);
    }

    public function approved(): static
    {
        return $this->state(fn (): array => [
            'status' => ApprovalStatus::Approved,
            'decided_at' => now(),
        ]);
    }

    public function rejected(string $comment): static
    {
        return $this->state(fn (): array => [
            'status' => ApprovalStatus::Rejected,
            'comment' => $comment,
            'decided_at' => now(),
        ]);
    }
}
