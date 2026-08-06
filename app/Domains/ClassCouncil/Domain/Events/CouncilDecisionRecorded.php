<?php

declare(strict_types=1);

namespace App\Domains\ClassCouncil\Domain\Events;

use App\Domains\ClassCouncil\Infrastructure\Models\CouncilDecision;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CouncilDecisionRecorded
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly CouncilDecision $decision,
        public readonly ?User $actor = null,
    ) {
    }
}
