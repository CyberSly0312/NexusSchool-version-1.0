<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Domain\References;

/**
 * Read-only projection of a class section owned by the Academic framework
 * domain (SPR-012). Free of any concrete model.
 */
final readonly class SectionReference
{
    public function __construct(
        public int|string $id,
        public string $name,
        public ?string $code = null,
    ) {
    }
}
