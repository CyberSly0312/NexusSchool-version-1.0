<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Domain\References;

/**
 * Read-only projection of the school (SPR-012). Free of any concrete model;
 * produced by the SchoolContract adapter.
 */
final readonly class SchoolReference
{
    /**
     * @param  array<string, mixed>  $settings
     */
    public function __construct(
        public string $id,
        public string $name,
        public ?string $logoUrl = null,
        public ?string $address = null,
        public ?string $city = null,
        public ?string $phone = null,
        public ?string $email = null,
        public ?string $registrationNumber = null,
        public ?string $schoolCode = null,
        public ?string $motto = null,
        public array $settings = [],
    ) {
    }

    public function code(): string
    {
        if ($this->schoolCode !== null && trim($this->schoolCode) !== '') {
            return strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $this->schoolCode));
        }

        $fallback = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $this->id));

        return 'SCH'.substr($fallback, -4);
    }

    public function fullAddress(): string
    {
        return trim(implode(', ', array_filter([$this->address, $this->city])));
    }
}
