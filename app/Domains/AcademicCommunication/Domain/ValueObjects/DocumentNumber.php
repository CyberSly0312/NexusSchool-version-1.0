<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Domain\ValueObjects;

use App\Domains\AcademicCommunication\Domain\Enums\DocumentType;
use InvalidArgumentException;

/**
 * Permanent identity of an AcademicDocument (SPR-012).
 *
 * Format: {platform}-{academicYear}-{schoolCode}-{documentTypeCode}-{sequence}
 *
 * Example: NS-2027-SCH001-RC-000000145
 *
 * The identity is assigned once at generation and never changes. Published
 * versions are immutable and never overwritten; corrections produce a new
 * version under the same identity.
 */
final readonly class DocumentNumber
{
    public function __construct(
        public string $platform,
        public string $academicYear,
        public string $schoolCode,
        public DocumentType $documentType,
        public int $sequence,
    ) {
    }

    public static function fromParts(
        string $platform,
        string $academicYear,
        string $schoolCode,
        DocumentType $documentType,
        int $sequence,
    ): self {
        if (trim($platform) === '' || trim($academicYear) === '' || trim($schoolCode) === '') {
            throw new InvalidArgumentException('DocumentNumber parts must not be empty.');
        }

        if ($sequence < 1) {
            throw new InvalidArgumentException('DocumentNumber sequence must be a positive integer.');
        }

        return new self(
            strtoupper(trim($platform)),
            trim($academicYear),
            strtoupper(trim($schoolCode)),
            $documentType,
            $sequence,
        );
    }

    public function toString(): string
    {
        return sprintf(
            '%s-%s-%s-%s-%s',
            $this->platform,
            $this->academicYear,
            $this->schoolCode,
            $this->documentType->code(),
            str_pad((string) $this->sequence, 9, '0', STR_PAD_LEFT),
        );
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * Parse a previously issued identity string.
     */
    public static function fromString(string $value): self
    {
        $pattern = '/^([A-Z0-9]+)-([A-Z0-9]+)-([A-Z0-9]+)-([A-Z]{2})-(\d{1,9})$/';

        if (! preg_match($pattern, strtoupper(trim($value)), $matches)) {
            throw new InvalidArgumentException("Invalid DocumentNumber format: [{$value}].");
        }

        $type = self::typeFromCode($matches[4]);

        return new self(
            $matches[1],
            $matches[2],
            $matches[3],
            $type,
            (int) $matches[5],
        );
    }

    private static function typeFromCode(string $code): DocumentType
    {
        foreach (DocumentType::cases() as $type) {
            if ($type->code() === $code) {
                return $type;
            }
        }

        throw new InvalidArgumentException("Unknown DocumentType code: [{$code}].");
    }
}
