<?php

declare(strict_types=1);

namespace App\Domains\User\DTOs;

use App\Models\UserInvitation;

final class UserInvitationIssue
{
    public function __construct(
        public readonly UserInvitation $invitation,
        public readonly string $plainToken,
    ) {
    }

    /**
     * @return array{invitation: UserInvitation}
     */
    public function __debugInfo(): array
    {
        return [
            'invitation' => $this->invitation,
            'plainToken' => '[redacted]',
        ];
    }
}
