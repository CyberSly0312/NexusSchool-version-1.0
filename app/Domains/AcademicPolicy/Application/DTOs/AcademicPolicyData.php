<?php

declare(strict_types=1);

namespace App\Domains\AcademicPolicy\Application\DTOs;

use App\Domains\AcademicPolicy\Domain\Enums\AcademicPolicyScope;
use App\Domains\AcademicPolicy\Domain\Enums\AcademicPolicyValueType;

/**
 * Create/update input of an academic policy (SPR-009.5).
 *
 * The scope determines whether the policy applies to the whole school, to one
 * academic year or to one subject. A school-scoped policy must not carry a
 * scope reference; a year- or subject-scoped policy must.
 */
final readonly class AcademicPolicyData
{
    public function __construct(
        public string $schoolId,
        public string $key,
        public string $label,
        public AcademicPolicyValueType $valueType,
        public mixed $value,
        public AcademicPolicyScope $scope = AcademicPolicyScope::School,
        public ?string $description = null,
        public ?int $academicYearId = null,
        public ?int $subjectId = null,
        public bool $isActive = true,
    ) {
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            schoolId: (string) $data['school_id'],
            key: (string) $data['key'],
            label: (string) $data['label'],
            valueType: AcademicPolicyValueType::from((string) $data['value_type']),
            value: $data['value'],
            scope: AcademicPolicyScope::from((string) ($data['scope'] ?? AcademicPolicyScope::School->value)),
            description: $data['description'] ?? null,
            academicYearId: isset($data['academic_year_id']) ? (int) $data['academic_year_id'] : null,
            subjectId: isset($data['academic_subject_id']) ? (int) $data['academic_subject_id'] : null,
            isActive: (bool) ($data['is_active'] ?? true),
        );
    }
}
