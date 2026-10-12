<?php

declare(strict_types=1);

namespace App\Domains\CertificateAndTranscript\Domain\Enums;

/**
 * Eligibility verdict of an end-of-study certificate (SPR-015).
 *
 * The verdict is orchestrated from authoritative, published evidence — a
 * published council decision, a graduation record, policy thresholds and the
 * published report-card snapshots. It is never recomputed from gradebooks.
 */
enum CertificateEligibilityVerdict: string
{
    case Eligible = 'eligible';
    case Ineligible = 'ineligible';

    public function label(): string
    {
        return match ($this) {
            self::Eligible => 'Éligible',
            self::Ineligible => 'Non éligible',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Eligible => 'success',
            self::Ineligible => 'danger',
        };
    }

    /**
     * @return array<int, string>
     */
    public static function options(): array
    {
        return array_map(
            static fn (self $verdict): string => $verdict->label(),
            self::cases(),
        );
    }
}
