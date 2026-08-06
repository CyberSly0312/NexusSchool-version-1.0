<?php

declare(strict_types=1);

namespace App\Domains\ReportCard\Domain\References;

/**
 * An academic policy applied to the period, rendered on the bulletin (SPR-011).
 */
final readonly class PolicyReference
{
    public function __construct(
        public string $code,
        public string $label,
        public ?string $description = null,
    ) {
    }
}
