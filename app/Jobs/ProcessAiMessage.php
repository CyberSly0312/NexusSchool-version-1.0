<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Domains\Ai\Enums\AiMessageRole;
use App\Models\AiConversation;
use App\Models\AiMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessAiMessage implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public int $timeout = 120;

    public function __construct(
        public string $conversationId,
        public string $content,
        public string $role = 'user',
    ) {
    }

    public function handle(): void
    {
        $conversation = AiConversation::findOrFail($this->conversationId);

        if ($conversation->status !== 'active') {
            return;
        }

        $userMessage = AiMessage::create([
            'school_id' => $conversation->school_id,
            'ai_conversation_id' => $conversation->id,
            'role' => AiMessageRole::User->value,
            'content' => $this->content,
            'input_tokens' => 0,
            'output_tokens' => 0,
        ]);

        $conversation->update([
            'message_count' => $conversation->message_count + 1,
        ]);

        $responseContent = $this->generateResponse($conversation);

        $assistantMessage = AiMessage::create([
            'school_id' => $conversation->school_id,
            'ai_conversation_id' => $conversation->id,
            'role' => AiMessageRole::Assistant->value,
            'content' => $responseContent,
            'input_tokens' => (int) ceil(str_word_count($this->content) * 1.3),
            'output_tokens' => (int) ceil(str_word_count($responseContent) * 1.3),
            'model' => $conversation->model,
            'finish_reason' => 'stop',
        ]);

        $conversation->update([
            'message_count' => $conversation->message_count + 1,
            'total_input_tokens' => $conversation->total_input_tokens + $assistantMessage->input_tokens,
            'total_output_tokens' => $conversation->total_output_tokens + $assistantMessage->output_tokens,
        ]);
    }

    private function generateResponse(AiConversation $conversation): string
    {
        return '[AI Platform Extension Point] This is a placeholder response. '
            . 'Implement provider-specific driver to generate actual AI responses. '
            . "Conversation: {$conversation->id}";
    }

    public function failed(\Throwable $exception): void
    {
        \Illuminate\Support\Facades\Log::error('AI Message Processing Failed', [
            'conversation_id' => $this->conversationId,
            'error' => $exception->getMessage(),
        ]);
    }
}
