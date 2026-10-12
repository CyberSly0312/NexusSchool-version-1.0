<?php

declare(strict_types=1);

namespace App\Domains\AcademicPolicy\Domain\References;

use App\Domains\AcademicPolicy\Domain\Enums\AcademicPolicyScope;
use App\Domains\AcademicPolicy\Domain\Enums\AcademicPolicyValueType;

/**
 * One validated academic policy consumed by downstream engines (SPR-009.5).
 *
 * This is the read-only shape the engine exposes through its query contract.
 * The scope narrows the policy to a school, an academic year or a subject; the
 * value is always typed according to its declared type.
 */
final readonly class AcademicPolicyReference
{
    public function __construct(
        public string $key,
        public string $label,
        public mixed $value,
        public AcademicPolicyValueType $valueType,
        public AcademicPolicyScope $scope,
        public ?string $description = null,
        public ?int $academicYearId = null,
        public ?int $subjectId = null,
    ) {
    }
}
