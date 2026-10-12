<?php

declare(strict_types=1);

namespace App\Domains\CertificateAndTranscript\Domain\References;

/**
 * An academic policy consumed for a certificate eligibility threshold (SPR-015,
 * supplied by the Academic Policies engine).
 */
final readonly class PolicyReference
{
    public function __construct(
        public string $key,
        public string $label,
        public mixed $value,
        public ?string $valueType = null,
        public ?string $description = null,
    ) {
    }
}
