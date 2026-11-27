<?php

declare(strict_types=1);

namespace App\Domains\IdentityMatching\Application\Services;

use Illuminate\Support\Str;

/**
 * Shared normalization for identity comparison only.
 *
 * Normalization never verifies ownership of an email address or phone number,
 * and never upgrades a weak signal into durable identity evidence. Email
 * normalization intentionally mirrors AccountProvisioningService (lowercase
 * and trim).
 */
final class IdentityNormalizer
{
    public function normalizeEmail(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $normalized = Str::lower(trim($value));

        return $normalized === '' ? null : $normalized;
    }

    /**
     * Digits-only comparison form. This is explicitly unverified.
     */
    public function normalizePhone(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', trim($value)) ?? '';

        return $digits === '' ? null : $digits;
    }

    public function normalizeName(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $trimmed = preg_replace('/\s+/u', ' ', trim($value)) ?? trim($value);
        $normalized = Str::lower($trimmed);

        return $normalized === '' ? null : $normalized;
    }

    public function normalizeIdentifier(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $normalized = trim($value);

        return $normalized === '' ? null : $normalized;
    }

    public function normalizeDate(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $normalized = trim($value);

        return $normalized === '' ? null : $normalized;
    }
}
