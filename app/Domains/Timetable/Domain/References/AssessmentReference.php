<?php

declare(strict_types=1);

namespace App\Domains\Timetable\Domain\References;

/**
 * Read-only projection of an assessment owned by the future Assessment engine.
 * Today the adapter maps the legacy assessment model; nothing may depend on it.
 * Free of any concrete model.
 */
final readonly class AssessmentReference
{
    public function __construct(
        public string $id,
        public string $name,
        public ?string $code = null,
        public int|string|null $subjectId = null,
        public bool $isActive = false,
    ) {
    }
}
