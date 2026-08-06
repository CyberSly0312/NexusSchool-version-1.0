<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Domain\Enums;

/**
 * Signature kinds supported by the Academic Communication Engine (SPR-012).
 *
 * Manual signatures are captured on the printed document; electronic and
 * digital signatures are stored on the document record and future-proofed for
 * a real digital-signature provider. The QR code signature carries the
 * verification token embedded in the generated file.
 */
enum SignatureType: string
{
    case Manual = 'manual';
    case Electronic = 'electronic';
    case Digital = 'digital';
    case QrCode = 'qr_code';

    public function label(): string
    {
        return match ($this) {
            self::Manual => 'Signature manuscrite',
            self::Electronic => 'Signature électronique',
            self::Digital => 'Signature numérique',
            self::QrCode => 'QR de vérification',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return array_map(
            static fn (self $type): string => $type->label(),
            self::cases(),
        );
    }
}
