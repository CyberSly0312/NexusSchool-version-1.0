<?php

declare(strict_types=1);

namespace App\Domains\Analytics\Events;

use App\Models\AnalyticsSnapshot;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SnapshotCreated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly AnalyticsSnapshot $snapshot,
    ) {
    }
}
