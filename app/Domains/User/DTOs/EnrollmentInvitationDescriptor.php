<?php

declare(strict_types=1);

namespace App\Domains\User\DTOs;

use App\Domains\User\Enums\EnrollmentInvitationLifecycle;

/**
 * Non-secret invitation projection returned to the application layer.
 *
 * It never carries a plaintext token, token hash, password or contact detail.
 */
final readonly class EnrollmentInvitationDescriptor
{
    public function __construct(
        public string $invitationId,
        public string $purpose,
        public EnrollmentInvitationLifecycle $lifecycle,
        public string $expiresAt,
        public bool $usable,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function __debugInfo(): array
    {
        return [
            'invitationId' => $this->invitationId,
            'purpose' => $this->purpose,
            'lifecycle' => $this->lifecycle->value,
            'expiresAt' => $this->expiresAt,
            'usable' => $this->usable,
        ];
    }
}
