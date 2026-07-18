<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Domains\Api\Services\ApiClientService;
use App\Http\Controllers\Controller;
use App\Http\Resources\ApiClientResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\ValidationException;

class ApiClientController extends Controller
{
    public function __construct(
        protected ApiClientService $service,
    ) {
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorizeClient($request, 'api.clients.view');

        $filters = $request->only(['search', 'is_active']);
        $filters['school_id'] = $request->input('api_client')->school_id;
        $clients = $this->service->paginate($filters, $request->get('per_page', 15));

        return ApiClientResource::collection($clients);
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorizeClient($request, 'api.clients.create');

        $data = array_merge($request->all(), ['school_id' => (string) $request->input('api_client')->school_id]);
        $clientData = $this->service->create($data);
        $client = $this->service->findById($clientData->id);

        return (new ApiClientResource($client))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Request $request, string $id): ApiClientResource
    {
        $this->authorizeClient($request, 'api.clients.view');

        $client = $this->service->findById($id);
        $this->ensureSchoolAccess($request, $client);

        return new ApiClientResource($client);
    }

    public function update(Request $request, string $id): ApiClientResource
    {
        $this->authorizeClient($request, 'api.clients.update');

        $client = $this->service->findById($id);
        $this->ensureSchoolAccess($request, $client);
        $this->service->update($client, $request->all());

        return new ApiClientResource($client->fresh());
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        $this->authorizeClient($request, 'api.clients.delete');

        $client = $this->service->findById($id);
        $this->ensureSchoolAccess($request, $client);
        $this->service->delete($client);

        return response()->json(['message' => 'API client deleted.'], 200);
    }

    public function disable(Request $request, string $id): ApiClientResource
    {
        $this->authorizeClient($request, 'api.clients.update');

        $client = $this->service->findById($id);
        $this->ensureSchoolAccess($request, $client);
        $this->service->disable($client);

        return new ApiClientResource($client->fresh());
    }

    public function enable(Request $request, string $id): ApiClientResource
    {
        $this->authorizeClient($request, 'api.clients.update');

        $client = $this->service->findById($id);
        $this->ensureSchoolAccess($request, $client);
        $this->service->enable($client);

        return new ApiClientResource($client->fresh());
    }

    public function regenerateSecret(Request $request, string $id): ApiClientResource
    {
        $this->authorizeClient($request, 'api.clients.update');

        $client = $this->service->findById($id);
        $this->ensureSchoolAccess($request, $client);
        $this->service->regenerateSecret($client);

        return new ApiClientResource($client->fresh());
    }

    private function authorizeClient(Request $request, string $permission): void
    {
        $token = $request->input('api_token');
        $abilities = $token->abilities ?? [];

        if (! in_array('*', $abilities) && ! in_array($permission, $abilities)) {
            throw ValidationException::withMessages([
                'message' => ['Insufficient permissions for this action.'],
            ]);
        }
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
}
