<?php

declare(strict_types=1);

namespace App\Domains\Analytics\Events;

use App\Models\Dashboard;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DashboardCreated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly Dashboard $dashboard,
    ) {
    }
}
