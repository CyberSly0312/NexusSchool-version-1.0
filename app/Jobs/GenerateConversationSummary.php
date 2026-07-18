<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\AiConversation;
use App\Models\AiMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateConversationSummary implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public int $timeout = 120;

    public function __construct(
        public string $conversationId,
    ) {
    }

    public function handle(): void
    {
        $conversation = AiConversation::findOrFail($this->conversationId);

        $messages = AiMessage::where('ai_conversation_id', $this->conversationId)
            ->where('is_archived', false)
            ->orderBy('created_at')
            ->get();

        if ($messages->isEmpty()) {
            return;
        }

        $summary = $this->generateSummary($messages);

        $conversation->update([
            'summary' => $summary,
            'status' => 'summarized',
        ]);
    }

    private function generateSummary($messages): string
    {
        $roles = $messages->groupBy('role');
        $userMessages = $roles->get('user', collect());
        $assistantMessages = $roles->get('assistant', collect());

        $topics = $userMessages->pluck('content')->map(function ($content) {
            return substr($content, 0, 100);
        })->toArray();

        return 'Conversation with ' . $messages->count() . ' messages. '
            . 'User messages: ' . $userMessages->count() . '. '
            . 'Assistant responses: ' . $assistantMessages->count() . '. '
            . 'Topics discussed: ' . implode('; ', array_slice($topics, 0, 5));
    }

    public function failed(\Throwable $exception): void
    {
        \Illuminate\Support\Facades\Log::error('Conversation Summary Generation Failed', [
            'conversation_id' => $this->conversationId,
            'error' => $exception->getMessage(),
        ]);
    }
}
