<?php

declare(strict_types=1);

namespace App\Domains\Ai\Services;

use App\Domains\Ai\DTOs\AiConversationData;
use App\Domains\Ai\DTOs\AiMessageData;
use App\Domains\Ai\Enums\AiConversationStatus;
use App\Domains\Ai\Events\ConversationStarted;
use App\Domains\Ai\Rules\AiConversationRules;
use App\Models\AiConversation;
use App\Models\AiMessage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ConversationService
{
    public function __construct(
        protected AiConversation $model = new AiConversation(),
        protected AiMessage $messageModel = new AiMessage(),
    ) {
    }

    public function create(array $data): AiConversationData
    {
        $validated = AiConversationRules::validate($data, 'create');
        $validated['status'] = $validated['status'] ?? AiConversationStatus::Active->value;
        $validated['context_window_limit'] = $validated['context_window_limit']
            ?? config('ai.conversation.default_context_window', 128000);

        $conversation = DB::transaction(function () use ($validated) {
            $conversation = $this->model->create($validated);

            return $conversation;
        });

        ConversationStarted::dispatch($conversation);

        return AiConversationData::create($conversation->toArray());
    }

    public function update(AiConversation $conversation, array $data): AiConversationData
    {
        $validated = AiConversationRules::validate($data, 'update', $conversation->id);

        $conversation->update($validated);

        return AiConversationData::create($conversation->fresh()->toArray());
    }

    public function archive(AiConversation $conversation): AiConversationData
    {
        if (! $conversation->status->canTransitionTo(AiConversationStatus::Archived)) {
            throw new \InvalidArgumentException(
                "Cannot transition from '{$conversation->status->value}' to 'archived'."
            );
        }

        $conversation->update(['status' => AiConversationStatus::Archived->value]);

        return AiConversationData::create($conversation->fresh()->toArray());
    }

    public function close(AiConversation $conversation): AiConversationData
    {
        if (! $conversation->status->canTransitionTo(AiConversationStatus::Closed)) {
            throw new \InvalidArgumentException(
                "Cannot transition from '{$conversation->status->value}' to 'closed'."
            );
        }

        $conversation->update(['status' => AiConversationStatus::Closed->value]);

        return AiConversationData::create($conversation->fresh()->toArray());
    }

    public function summarize(AiConversation $conversation, string $summary): AiConversationData
    {
        $conversation->update([
            'summary' => $summary,
            'status' => AiConversationStatus::Summarized->value,
        ]);

        return AiConversationData::create($conversation->fresh()->toArray());
    }

    public function addMessage(AiConversation $conversation, array $data): AiMessageData
    {
        $data['school_id'] = $conversation->school_id;
        $data['ai_conversation_id'] = $conversation->id;

        $message = DB::transaction(function () use ($conversation, $data) {
            $message = $this->messageModel->create($data);

            $conversation->update([
                'message_count' => $conversation->message_count + 1,
                'total_input_tokens' => $conversation->total_input_tokens + ($data['input_tokens'] ?? 0),
                'total_output_tokens' => $conversation->total_output_tokens + ($data['output_tokens'] ?? 0),
                'context_window_used' => $conversation->context_window_used
                    + ($data['input_tokens'] ?? 0) + ($data['output_tokens'] ?? 0),
            ]);

            return $message;
        });

        return AiMessageData::create($message->toArray());
    }

    public function getMessages(string $conversationId, int $limit = 50): Collection
    {
        return $this->messageModel
            ->where('ai_conversation_id', $conversationId)
            ->where('is_archived', false)
            ->orderBy('created_at')
            ->limit($limit)
            ->get();
    }

    public function getMessageHistory(string $conversationId): array
    {
        $messages = $this->getMessages($conversationId, $this->getContextWindowMessages($conversationId));

        return $messages->map(fn (AiMessage $msg) => [
            'role' => $msg->role,
            'content' => $msg->content,
        ])->toArray();
    }

    public function findById(string $id): AiConversation
    {
        return $this->model->with(['provider', 'user'])->findOrFail($id);
    }

    public function delete(AiConversation $conversation): void
    {
        $conversation->delete();
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->with(['provider', 'user']);

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('summary', 'like', "%{$search}%");
            });
        }

        if (isset($filters['school_id'])) {
            $query->where('school_id', $filters['school_id']);
        }

        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['ai_provider_id'])) {
            $query->where('ai_provider_id', $filters['ai_provider_id']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function getActiveConversations(string $schoolId): Collection
    {
        return $this->model
            ->where('school_id', $schoolId)
            ->where('status', AiConversationStatus::Active->value)
            ->with('provider')
            ->latest()
            ->get();
    }

    public function needsSummarization(AiConversation $conversation): bool
    {
        $threshold = config('ai.conversation.summarize_threshold', 50);

        return $conversation->message_count >= $threshold && $conversation->summary === null;
    }

    private function getContextWindowMessages(string $conversationId): int
    {
        $conversation = $this->model->findOrFail($conversationId);
        $avgTokens = max(1, (int) ($conversation->context_window_used / max(1, $conversation->message_count)));

        return (int) ($conversation->context_window_limit / $avgTokens);
    }
}
