<?php

declare(strict_types=1);

namespace App\Domains\Ai\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UsageQuotaExceeded
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly string $schoolId,
        public readonly string $quotaType,
        public readonly int $currentUsage,
        public readonly int $limit,
    ) {
    }
}
