<?php

declare(strict_types=1);

namespace Database\Factories\AcademicCommunication;

use App\Domains\AcademicCommunication\Domain\Enums\DocumentType;
use App\Domains\AcademicCommunication\Domain\Enums\VisibilityScope;
use App\Domains\AcademicCommunication\Infrastructure\Models\PublicationPolicy;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PublicationPolicy>
 */
class PublicationPolicyFactory extends Factory
{
    protected $model = PublicationPolicy::class;

    public function definition(): array
    {
        return [
            'school_id' => 'fac-'.(string) \Illuminate\Support\Str::ulid(),
            'name' => fake()->unique()->words(3, true),
            'code' => fake()->unique()->lexify('POL-????-????'),
            'document_type' => DocumentType::ReportCard,
            'recipient_scopes' => [VisibilityScope::Director->value, VisibilityScope::Parent->value],
            'channels' => ['parent_portal', 'internal_portal'],
            'requires_approval' => false,
            'approval_roles' => [],
            'visibility' => [
                VisibilityScope::Director->value => ['sections' => ['*']],
                VisibilityScope::Secretary->value => ['sections' => ['administrative']],
                VisibilityScope::Teacher->value => ['sections' => ['teaching', 'student']],
                VisibilityScope::Parent->value => ['sections' => ['student', 'results'], 'self_only' => true],
                VisibilityScope::Student->value => ['sections' => ['student', 'results'], 'self_only' => true],
            ],
            'is_active' => true,
            'is_default' => false,
            'effective_from' => null,
            'effective_until' => null,
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

    public function requiringApproval(array $roles): static
    {
        return $this->state(fn (): array => [
            'requires_approval' => true,
            'approval_roles' => $roles,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (): array => ['is_active' => false]);
    }
}
