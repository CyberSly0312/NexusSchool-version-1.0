<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiAuditLogResource;
use App\Models\ApiAuditLog;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AuditLogController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = ApiAuditLog::query();

        $client = $request->input('api_client');
        $query->where('school_id', $client->school_id);

        if ($request->has('api_client_id')) {
            $query->where('api_client_id', $request->input('api_client_id'));
        }

        if ($request->has('method')) {
            $query->where('method', strtoupper($request->input('method')));
        }

        if ($request->has('status_code')) {
            $query->where('status_code', $request->input('status_code'));
        }

        if ($request->has('from_date')) {
            $query->where('created_at', '>=', $request->input('from_date'));
        }

        if ($request->has('to_date')) {
            $query->where('created_at', '<=', $request->input('to_date'));
        }

        $perPage = min((int) $request->get('per_page', 15), 100);

        $logs = $query->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return ApiAuditLogResource::collection($logs);
    }

    public function show(Request $request, string $id): ApiAuditLogResource
    {
        $log = ApiAuditLog::where('school_id', $request->input('api_client')->school_id)
            ->findOrFail($id);

        return new ApiAuditLogResource($log);
    }
}
