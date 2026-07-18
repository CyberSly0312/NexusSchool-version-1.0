<?php

declare(strict_types=1);

namespace App\Domains\Api\Services;

use App\Domains\Api\DTOs\WebhookData;
use App\Domains\Api\DTOs\WebhookDeliveryData;
use App\Domains\Api\Events\WebhookDelivered;
use App\Domains\Api\Events\WebhookFailed;
use App\Domains\Api\Events\WebhookRegistered;
use App\Domains\Api\Rules\WebhookRules;
use App\Models\ApiClient;
use App\Models\Webhook;
use App\Models\WebhookDelivery;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WebhookService
{
    public function __construct(
        protected Webhook $model = new Webhook(),
        protected WebhookDelivery $deliveryModel = new WebhookDelivery(),
    ) {
    }

    public function create(ApiClient $client, array $data): WebhookData
    {
        $validated = WebhookRules::validate($data, 'create');
        $validated['api_client_id'] = $client->id;
        $validated['school_id'] = $client->school_id;
        $validated['secret'] = Str::random(64);

        return DB::transaction(function () use ($validated) {
            $webhook = $this->model->create($validated);
            WebhookRegistered::dispatch($webhook->fresh());

            return WebhookData::create($webhook->toArray());
        });
    }

    public function update(Webhook $webhook, array $data): WebhookData
    {
        $validated = WebhookRules::validate($data, 'update', $webhook->id);
        $webhook->update($validated);

        return WebhookData::create($webhook->fresh()->toArray());
    }

    public function delete(Webhook $webhook): void
    {
        $webhook->delete();
    }

    public function disable(Webhook $webhook): WebhookData
    {
        $webhook->update(['is_active' => false]);

        return WebhookData::create($webhook->fresh()->toArray());
    }

    public function enable(Webhook $webhook): WebhookData
    {
        $webhook->update(['is_active' => true]);

        return WebhookData::create($webhook->fresh()->toArray());
    }

    public function findById(string $id): Webhook
    {
        return $this->model->with(['deliveries'])->findOrFail($id);
    }

    public function getActiveForEvent(string $schoolId, string $event): Collection
    {
        return $this->model->where('school_id', $schoolId)
            ->where('is_active', true)
            ->whereJsonContains('events', $event)
            ->get();
    }

    public function paginateByClient(string $clientId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->where('api_client_id', $clientId);

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('url', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function recordDelivery(Webhook $webhook, string $event, array $payload, ?int $statusCode, ?string $responseBody, bool $success): WebhookDeliveryData
    {
        $delivery = $this->deliveryModel->create([
            'webhook_id' => $webhook->id,
            'school_id' => $webhook->school_id,
            'event' => $event,
            'payload' => $payload,
            'response_status' => $statusCode,
            'response_body' => $responseBody,
            'success' => $success,
            'delivered_at' => $success ? now() : null,
            'retry_count' => 0,
        ]);

        $webhook->update(['last_triggered_at' => now()]);

        if ($success) {
            WebhookDelivered::dispatch($webhook, $delivery);
        } else {
            $webhook->increment('failure_count');
            WebhookFailed::dispatch($webhook, $delivery);
        }

        return WebhookDeliveryData::create($delivery->toArray());
    }

    public function getDeliveries(Webhook $webhook, int $perPage = 15): LengthAwarePaginator
    {
        return $this->deliveryModel->where('webhook_id', $webhook->id)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}
