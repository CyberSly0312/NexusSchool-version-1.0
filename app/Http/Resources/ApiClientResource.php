<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApiClientResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'is_active' => $this->is_active,
            'scopes' => $this->scopes,
            'rate_limit_per_minute' => $this->rate_limit_per_minute,
            'rate_limit_per_day' => $this->rate_limit_per_day,
            'tokens_count' => $this->whenCounted('tokens'),
            'webhooks_count' => $this->whenCounted('webhooks'),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
