<?php

declare(strict_types=1);

namespace App\Jobs\Workflow;

use App\Domains\Workflow\Services\AutomationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TriggerAutomationRules implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public int $timeout = 60;

    public function __construct(
        public string $eventName,
        public array $context = [],
    ) {
    }

    public function handle(AutomationService $automationService): void
    {
        $automationService->triggerRules($this->eventName, $this->context);
    }

    public function failed(\Throwable $exception): void
    {
        \Illuminate\Support\Facades\Log::error('Automation Rule Trigger Failed', [
            'event_name' => $this->eventName,
            'error' => $exception->getMessage(),
        ]);
    }
}
