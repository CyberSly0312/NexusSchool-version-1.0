<?php

declare(strict_types=1);

namespace App\Domains\Staff\Domain\Events;

use App\Domains\Staff\Infrastructure\Models\Assignment;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PositionAssigned
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly Assignment $assignment,
        public readonly ?User $actor = null,
    ) {
    }
}
