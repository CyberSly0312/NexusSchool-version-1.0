<?php

declare(strict_types=1);

namespace App\Domains\Workflow\Events;

use App\Models\AutomationRule;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AutomationTriggered
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly AutomationRule $rule,
        public readonly array $context = [],
    ) {
    }
}
