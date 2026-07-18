<?php

declare(strict_types=1);

namespace App\Domains\Ai\Listeners;

use App\Domains\Ai\Services\AiService;
use App\Domains\Shared\Traits\HasEntityExtraction;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

class UsageTracker
{
    use Dispatchable;
    use HasEntityExtraction;
    use InteractsWithQueue;

    public function __construct(
        protected AiService $aiService,
    ) {
    }

    public function handle(object $event): void
    {
        $entity = $this->extractEntity($event);
        $eventName = class_basename($event);

        $this->aiService->recordUsage([
            'school_id' => $entity->school_id ?? null,
            'ai_provider_id' => $entity->ai_provider_id ?? null,
            'ai_conversation_id' => $entity->ai_conversation_id ?? null,
            'user_id' => $entity->user_id ?? null,
            'action' => $eventName,
            'model' => $entity->model ?? null,
            'input_tokens' => $entity->input_tokens ?? 0,
            'output_tokens' => $entity->output_tokens ?? 0,
            'is_billable' => true,
            'status' => 'success',
        ]);
    }
}
