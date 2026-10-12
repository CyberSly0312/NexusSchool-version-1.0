<?php

declare(strict_types=1);

namespace App\Domains\IdentityAndVerification\Domain\Events;

use App\Domains\IdentityAndVerification\Infrastructure\Models\VerificationIdentity;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VerificationIdentityReplaced
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly VerificationIdentity $identity,
        public readonly int|string $successorDocumentId,
        public readonly string $successorDocumentNumber,
        public readonly string $reason,
        public readonly ?User $actor = null,
    ) {
    }
}
