<?php

declare(strict_types=1);

namespace App\Domains\Workflow\Events;

use App\Models\WorkflowTask;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskCompleted
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly WorkflowTask $task,
    ) {
    }
}
