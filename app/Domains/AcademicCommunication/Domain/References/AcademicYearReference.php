<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Domain\References;

/**
 * Read-only projection of an academic year owned by the Academic framework
 * domain (SPR-012). Free of any concrete model.
 */
final readonly class AcademicYearReference
{
    public function __construct(
        public int|string $id,
        public string $name,
        public ?string $startDate = null,
        public ?string $endDate = null,
        public string $status = '',
    ) {
    }

    /**
     * The year token used in the permanent document identity (ending year of
     * the academic year, e.g. 2027 for 2026-2027). Falls back to the name's
     * trailing year, then the current year.
     */
    public function identityYear(): string
    {
        if ($this->endDate !== null && preg_match('/(\d{4})/', $this->endDate, $m)) {
            return $m[1];
        }

        if (preg_match('/(\d{4})\s*-\s*(\d{4})/', $this->name, $m)) {
            return $m[2];
        }

        return (string) now()->year;
    }
}
