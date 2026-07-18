<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WebhookResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'url' => $this->url,
            'events' => $this->events,
            'is_active' => $this->is_active,
            'description' => $this->description,
            'failure_count' => $this->failure_count,
            'last_triggered_at' => $this->last_triggered_at?->toDateTimeString(),
            'deliveries_count' => $this->whenCounted('deliveries'),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
