<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Domain\ValueObjects;

use App\Domains\AcademicCommunication\Domain\Enums\SignatureType;

/**
 * One signature slot required by a document template (SPR-012). Signature areas
 * are part of the template branding (e.g. Principal, Secretary, Council
 * President) and may carry a QR-code verification placeholder.
 */
final readonly class SignatureArea
{
    public function __construct(
        public string $role,
        public string $label,
        public SignatureType $type = SignatureType::Electronic,
        public bool $includeQrCode = false,
    ) {
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        $type = SignatureType::tryFrom((string) ($data['type'] ?? SignatureType::Electronic->value))
            ?? SignatureType::Electronic;

        return new self(
            (string) $data['role'],
            (string) ($data['label'] ?? $data['role']),
            $type,
            (bool) ($data['qr_code'] ?? false),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'role' => $this->role,
            'label' => $this->label,
            'type' => $this->type->value,
            'qr_code' => $this->includeQrCode,
        ];
    }
}
