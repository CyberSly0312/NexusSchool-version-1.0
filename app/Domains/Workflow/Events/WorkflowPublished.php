<?php

declare(strict_types=1);

namespace App\Domains\Workflow\Events;

use App\Models\Workflow;
use App\Models\WorkflowVersion;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WorkflowPublished
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly Workflow $workflow,
        public readonly WorkflowVersion $version,
    ) {
    }
}
