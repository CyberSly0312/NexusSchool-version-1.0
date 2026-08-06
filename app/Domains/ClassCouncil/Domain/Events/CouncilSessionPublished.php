<?php

declare(strict_types=1);

namespace App\Domains\ClassCouncil\Domain\Events;

use App\Domains\ClassCouncil\Infrastructure\Models\CouncilSession;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CouncilSessionPublished
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly CouncilSession $session,
        public readonly int $decisionsCount = 0,
        public readonly ?User $actor = null,
    ) {
    }
}
