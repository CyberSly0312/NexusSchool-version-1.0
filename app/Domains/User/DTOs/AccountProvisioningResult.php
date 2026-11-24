<?php

declare(strict_types=1);

namespace App\Domains\User\DTOs;

use App\Models\User;
use App\Models\UserInvitation;

final class AccountProvisioningResult
{
    public function __construct(
        public readonly User $user,
        public readonly UserInvitationIssue $invitationIssue,
    ) {
    }

    public function invitation(): UserInvitation
    {
        return $this->invitationIssue->invitation;
    }

    /**
     * @return array{user: User, invitation: UserInvitation, plainToken: string}
     */
    public function __debugInfo(): array
    {
        return [
            'user' => $this->user,
            'invitation' => $this->invitationIssue->invitation,
            'plainToken' => '[redacted]',
        ];
    }
}
