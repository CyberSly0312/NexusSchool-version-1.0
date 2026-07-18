<?php

declare(strict_types=1);

namespace App\Domains\Analytics\Events;

use App\Models\Kpi;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class KpiRecalculated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly Kpi $kpi,
    ) {
    }
}
