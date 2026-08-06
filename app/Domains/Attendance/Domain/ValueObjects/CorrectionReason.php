<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Domain\ValueObjects;

/**
 * Reason attached to an authorized correction. Carries a fixed vocabulary
 * (`type`) plus free-form detail text (SPR-008).
 */
final readonly class CorrectionReason
{
    public const TYPE_ERROR = 'data_entry_error';

    public const TYPE_EXCUSE_APPROVED = 'excuse_approved';

    public const TYPE_MEDICAL_CERTIFICATE = 'medical_certificate';

    public const TYPE_SCHOOL_ACTIVITY = 'school_activity';

    public const TYPE_ADMINISTRATIVE = 'administrative';

    public const TYPE_OTHER = 'other';

    public function __construct(
        public string $type = self::TYPE_OTHER,
        public string $text = '',
    ) {
        if ($text !== '' && mb_strlen($text) > 500) {
            throw new \InvalidArgumentException('Correction reason text cannot exceed 500 characters.');
        }
    }

    public static function forType(string $type, string $text = ''): self
    {
        $valid = [
            self::TYPE_ERROR,
            self::TYPE_EXCUSE_APPROVED,
            self::TYPE_MEDICAL_CERTIFICATE,
            self::TYPE_SCHOOL_ACTIVITY,
            self::TYPE_ADMINISTRATIVE,
            self::TYPE_OTHER,
        ];

        if (! in_array($type, $valid, true)) {
            throw new \InvalidArgumentException("Unknown correction reason type [{$type}].");
        }

        return new self($type, $text);
    }

    public function typeLabel(): string
    {
        return match ($this->type) {
            self::TYPE_ERROR => 'Erreur de saisie',
            self::TYPE_EXCUSE_APPROVED => 'Excuse approuvée',
            self::TYPE_MEDICAL_CERTIFICATE => 'Certificat médical',
            self::TYPE_SCHOOL_ACTIVITY => 'Activité scolaire',
            self::TYPE_ADMINISTRATIVE => 'Administratif',
            default => 'Autre',
        };
    }
}
