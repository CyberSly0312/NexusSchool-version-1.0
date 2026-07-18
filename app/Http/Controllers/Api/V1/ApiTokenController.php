<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Domains\Api\Services\ApiClientService;
use App\Domains\Api\Services\ApiTokenService;
use App\Http\Controllers\Controller;
use App\Http\Resources\ApiTokenResource;
use App\Models\ApiToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\ValidationException;

class ApiTokenController extends Controller
{
    public function __construct(
        protected ApiTokenService $service,
        protected ApiClientService $clientService,
    ) {
    }

    public function index(Request $request, string $clientId): AnonymousResourceCollection
    {
        $client = $this->clientService->findById($clientId);
        $this->ensureSchoolAccess($request, $client);

        $filters = $request->only(['search', 'is_active']);
        $tokens = $this->service->paginateByClient($clientId, $filters, $request->get('per_page', 15));

        return ApiTokenResource::collection($tokens);
    }

    public function store(Request $request, string $clientId): JsonResponse
    {
        $client = $this->clientService->findById($clientId);
        $this->ensureSchoolAccess($request, $client);
        $tokenData = $this->service->create($client, $request->all());
        $token = \App\Models\ApiToken::where('api_client_id', $clientId)->findOrFail($tokenData->id);

        return (new ApiTokenResource($token))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Request $request, string $clientId, string $tokenId): ApiTokenResource
    {
        $client = $this->clientService->findById($clientId);
        $this->ensureSchoolAccess($request, $client);
        $token = ApiToken::where('api_client_id', $clientId)->findOrFail($tokenId);

        return new ApiTokenResource($token);
    }

    public function destroy(Request $request, string $clientId, string $tokenId): JsonResponse
    {
        $client = $this->clientService->findById($clientId);
        $this->ensureSchoolAccess($request, $client);
        $token = ApiToken::where('api_client_id', $clientId)->findOrFail($tokenId);

        $this->ensureTokenOwnership($request, $token);
        $this->service->revoke($token);

        return response()->json(['message' => 'Token revoked.'], 200);
    }

    public function revoke(Request $request, string $clientId, string $tokenId): JsonResponse
    {
        $client = $this->clientService->findById($clientId);
        $this->ensureSchoolAccess($request, $client);
        $token = ApiToken::where('api_client_id', $clientId)->findOrFail($tokenId);

        $this->ensureTokenOwnership($request, $token);
        $this->service->revoke($token);

        return response()->json(['message' => 'Token revoked.'], 200);
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

    private function ensureTokenOwnership(Request $request, ApiToken $token): void
    {
        $user = $request->input('api_user');

        if ($user && $token->user_id !== $user->id) {
            throw ValidationException::withMessages([
                'message' => ['You can only manage your own tokens.'],
            ]);
        }
    }
}
