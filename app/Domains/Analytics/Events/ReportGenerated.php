<?php

declare(strict_types=1);

namespace App\Domains\Analytics\Events;

use App\Models\Report;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReportGenerated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly Report $report,
    ) {
    }
}
