<?php

declare(strict_types=1);

namespace App\Domains\Communication\Services;

use App\Domains\Communication\DTOs\MessageData;
use App\Domains\Communication\Enums\DeliveryStatus;
use App\Domains\Communication\Enums\MessageStatus;
use App\Domains\Communication\Enums\MessageType;
use App\Domains\Communication\Events\MessageSent;
use App\Domains\Communication\Rules\MessageRules;
use App\Models\Message;
use App\Models\MessageRecipient;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MessagingService
{
    public function __construct(
        protected MessageRules $messageRules,
    ) {
    }

    public function create(mixed $data): MessageData
    {
        $validated = $this->messageRules->validate($data, 'create');

        $recipientIds = $validated['recipient_ids'];
        unset($validated['recipient_ids']);

        $validated['status'] = $validated['status'] ?? MessageStatus::Draft->value;
        $validated['type'] = $validated['type'] ?? MessageType::Direct->value;

        $message = DB::transaction(function () use ($validated, $recipientIds) {
            $message = Message::create($validated);

            foreach ($recipientIds as $recipientId) {
                MessageRecipient::create([
                    'school_id' => $message->school_id,
                    'message_id' => $message->id,
                    'recipient_id' => $recipientId,
                    'status' => DeliveryStatus::Pending->value,
                ]);
            }

            return $message;
        });

        return MessageData::create($message->toArray());
    }

    public function send(string $id): MessageData
    {
        $message = Message::findOrFail($id);

        if (! $message->status->canTransitionTo(MessageStatus::Sent)) {
            throw new \InvalidArgumentException(
                "Cannot transition from '{$message->status->value}' to 'sent'."
            );
        }

        DB::transaction(function () use ($message) {
            $message->update([
                'status' => MessageStatus::Sent->value,
                'sent_at' => now(),
            ]);

            $message->recipients()->update([
                'status' => DeliveryStatus::Sent->value,
            ]);
        });

        MessageSent::dispatch($message);

        return MessageData::create($message->fresh()->toArray());
    }

    public function markDelivered(string $id): MessageData
    {
        $message = Message::findOrFail($id);

        $message->update(['status' => MessageStatus::Delivered->value]);

        return MessageData::create($message->fresh()->toArray());
    }

    public function markRecipientRead(string $messageId, string $recipientId): void
    {
        $recipient = MessageRecipient::where('message_id', $messageId)
            ->where('recipient_id', $recipientId)
            ->firstOrFail();

        $recipient->update([
            'read_at' => now(),
            'status' => DeliveryStatus::Delivered->value,
        ]);

        $message = Message::find($messageId);
        if ($message && ! $message->recipients()->whereNull('read_at')->exists()) {
            $message->update(['status' => MessageStatus::Read->value]);
        }
    }

    public function archive(string $id): MessageData
    {
        $message = Message::findOrFail($id);

        $message->update(['status' => MessageStatus::Archived->value]);

        return MessageData::create($message->fresh()->toArray());
    }

    public function delete(string $id): void
    {
        $message = Message::findOrFail($id);
        $message->delete();
    }

    public function getInbox(string $userId, ?string $schoolId = null): Collection
    {
        $query = Message::whereHas('recipients', function ($q) use ($userId) {
            $q->where('recipient_id', $userId);
        });

        if ($schoolId) {
            $query->where('school_id', $schoolId);
        }

        return $query->with('sender')->latest()->get();
    }

    public function getSentMessages(string $userId, ?string $schoolId = null): Collection
    {
        $query = Message::where('sender_id', $userId);

        if ($schoolId) {
            $query->where('school_id', $schoolId);
        }

        return $query->with('recipients')->latest()->get();
    }

    public function getUnreadCount(string $userId): int
    {
        return Message::whereHas('recipients', function ($q) use ($userId) {
            $q->where('recipient_id', $userId)
                ->whereNull('read_at');
        })->count();
    }
}
