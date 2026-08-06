<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Domain\References;

/**
 * One validated academic policy consumed from the Academic Policies engine
 * (SPR-012) — e.g. the grading scale, honor thresholds or the examination
 * rules that a document must reflect.
 */
final readonly class AcademicPolicyReference
{
    public function __construct(
        public string $key,
        public string $label,
        public mixed $value,
        public string $scope = 'school',
    ) {
    }
}
