<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Domains\Api\Services\ApiTokenService;
use App\Models\ApiToken;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PruneExpiredTokens implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 1;

    public int $timeout = 60;

    public function handle(ApiTokenService $tokenService): void
    {
        $expiredTokens = ApiToken::where('expires_at', '<', now())
            ->where('is_active', true)
            ->get();

        foreach ($expiredTokens as $token) {
            $tokenService->revoke($token);
        }

        Log::info('Pruned expired API tokens', [
            'count' => $expiredTokens->count(),
        ]);
    }
}
