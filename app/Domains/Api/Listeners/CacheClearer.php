<?php

declare(strict_types=1);

namespace App\Domains\Api\Listeners;

use App\Domains\Api\Events\ApiClientDisabled;
use App\Domains\Api\Events\ApiTokenRevoked;
use Illuminate\Support\Facades\Cache;

class CacheClearer
{
    public function handleApiTokenRevoked(ApiTokenRevoked $event): void
    {
        $token = $event->token;

        Cache::forget("api_token_{$token->id}");
        Cache::forget("api_client_tokens_{$token->api_client_id}");
        Cache::forget("school_api_tokens_{$token->school_id}");
    }

    public function handleApiClientDisabled(ApiClientDisabled $event): void
    {
        $client = $event->client;

        Cache::forget("api_client_{$client->id}");
        Cache::forget("school_api_clients_{$client->school_id}");
    }
}
