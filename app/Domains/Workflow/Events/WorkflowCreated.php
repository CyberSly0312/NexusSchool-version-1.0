<?php

declare(strict_types=1);

namespace App\Domains\Workflow\Events;

use App\Models\Workflow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WorkflowCreated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly Workflow $workflow,
    ) {
    }
}
