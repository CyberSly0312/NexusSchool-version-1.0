<?php

declare(strict_types=1);

namespace App\Domains\Workflow\Events;

use App\Models\WorkflowExecution;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WorkflowFailed
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly WorkflowExecution $execution,
        public readonly ?string $error = null,
    ) {
    }
}
