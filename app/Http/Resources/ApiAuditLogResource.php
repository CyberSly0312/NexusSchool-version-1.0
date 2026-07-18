<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApiAuditLogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'method' => $this->method,
            'path' => $this->path,
            'status_code' => $this->status_code,
            'ip_address' => $this->ip_address,
            'response_time_ms' => $this->response_time_ms,
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
