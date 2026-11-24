<?php

declare(strict_types=1);

namespace App\Domains\School\DTOs;

use App\Domains\User\DTOs\AccountProvisioningResult;
use App\Models\User;
use App\Models\UserInvitation;
use JsonSerializable;

/**
 * Presentation-safe result of the initial School Admin provisioning flow.
 *
 * The plaintext activation token lives only in the in-memory provisioning DTO
 * and in the one-time URL passed to the authorized operator. Neither is
 * persisted on the Livewire component, and debug/JSON output is redacted.
 */
final class InitialSchoolAdminProvisioningResult implements JsonSerializable
{
    public function __construct(
        public readonly User $admin,
        public readonly bool $created,
        public readonly ?AccountProvisioningResult $provisioning = null,
        public readonly ?string $activationUrl = null,
    ) {
    }

    public function invitation(): ?UserInvitation
    {
        return $this->provisioning?->invitation();
    }

    public function plainToken(): ?string
    {
        return $this->provisioning?->invitationIssue->plainToken;
    }

    /**
     * @return array{admin: User, created: bool, provisioning: ?AccountProvisioningResult, activationUrl: string}
     */
    public function __debugInfo(): array
    {
        return [
            'admin' => $this->admin,
            'created' => $this->created,
            'provisioning' => $this->provisioning,
            'activationUrl' => '[redacted]',
        ];
    }

    /**
     * @return array{admin: User, created: bool, provisioning: array{user: User, invitation: UserInvitation, plainToken: string}|null, activationUrl: string}
     */
    public function jsonSerialize(): array
    {
        $data = $this->__debugInfo();
        $data['provisioning'] = $this->provisioning?->__debugInfo();

        return $data;
    }
}
