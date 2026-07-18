<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Domains\Api\Services\ApiClientService;
use App\Domains\Api\Services\WebhookService;
use App\Http\Controllers\Controller;
use App\Http\Resources\WebhookDeliveryResource;
use App\Http\Resources\WebhookResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\ValidationException;

class WebhookController extends Controller
{
    public function __construct(
        protected WebhookService $service,
        protected ApiClientService $clientService,
    ) {
    }

    public function index(Request $request, string $clientId): AnonymousResourceCollection
    {
        $client = $this->clientService->findById($clientId);
        $this->ensureSchoolAccess($request, $client);

        $filters = $request->only(['search', 'is_active']);
        $webhooks = $this->service->paginateByClient($clientId, $filters, $request->get('per_page', 15));

        return WebhookResource::collection($webhooks);
    }

    public function store(Request $request, string $clientId): JsonResponse
    {
        $client = $this->clientService->findById($clientId);
        $this->ensureSchoolAccess($request, $client);
        $webhookData = $this->service->create($client, $request->all());
        $webhook = $this->service->findById($webhookData->id);

        return (new WebhookResource($webhook))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Request $request, string $clientId, string $webhookId): WebhookResource
    {
        $client = $this->clientService->findById($clientId);
        $this->ensureSchoolAccess($request, $client);
        $webhook = $this->service->findById($webhookId);
        $this->ensureWebhookBelongsToClient($webhook, $clientId);

        return new WebhookResource($webhook);
    }

    public function update(Request $request, string $clientId, string $webhookId): WebhookResource
    {
        $client = $this->clientService->findById($clientId);
        $this->ensureSchoolAccess($request, $client);
        $webhook = $this->service->findById($webhookId);
        $this->ensureWebhookBelongsToClient($webhook, $clientId);
        $this->service->update($webhook, $request->all());

        return new WebhookResource($webhook->fresh());
    }

    public function destroy(Request $request, string $clientId, string $webhookId): JsonResponse
    {
        $client = $this->clientService->findById($clientId);
        $this->ensureSchoolAccess($request, $client);
        $webhook = $this->service->findById($webhookId);
        $this->ensureWebhookBelongsToClient($webhook, $clientId);
        $this->service->delete($webhook);

        return response()->json(['message' => 'Webhook deleted.'], 200);
    }

    public function disable(Request $request, string $clientId, string $webhookId): WebhookResource
    {
        $client = $this->clientService->findById($clientId);
        $this->ensureSchoolAccess($request, $client);
        $webhook = $this->service->findById($webhookId);
        $this->ensureWebhookBelongsToClient($webhook, $clientId);
        $this->service->disable($webhook);

        return new WebhookResource($webhook->fresh());
    }

    public function enable(Request $request, string $clientId, string $webhookId): WebhookResource
    {
        $client = $this->clientService->findById($clientId);
        $this->ensureSchoolAccess($request, $client);
        $webhook = $this->service->findById($webhookId);
        $this->ensureWebhookBelongsToClient($webhook, $clientId);
        $this->service->enable($webhook);

        return new WebhookResource($webhook->fresh());
    }

    public function deliveries(Request $request, string $clientId, string $webhookId): AnonymousResourceCollection
    {
        $client = $this->clientService->findById($clientId);
        $this->ensureSchoolAccess($request, $client);
        $webhook = $this->service->findById($webhookId);
        $this->ensureWebhookBelongsToClient($webhook, $clientId);
        $deliveries = $this->service->getDeliveries($webhook, $request->get('per_page', 15));

        return WebhookDeliveryResource::collection($deliveries);
    }

    private function ensureSchoolAccess(Request $request, object $resource): void
    {
        $clientSchoolId = $request->input('api_client')->school_id;

        if (isset($resource->school_id) && $resource->school_id !== $clientSchoolId) {
            throw ValidationException::withMessages([
                'message' => ['Access denied to resource from another school.'],
            ]);
        }
    }

    private function ensureWebhookBelongsToClient(object $webhook, string $clientId): void
    {
        if (isset($webhook->api_client_id) && $webhook->api_client_id !== $clientId) {
            throw ValidationException::withMessages([
                'message' => ['Webhook does not belong to this client.'],
            ]);
        }
    }
}
