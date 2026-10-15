<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Restricts production traffic to central hosts and canonical Stancl domains.
 *
 * Local development and framework tests deliberately retain Laravel's flexible
 * host handling: school-panel middleware still resolves unknown hosts
 * fail-closed, while hosts-file and test-client workflows need no allowlist.
 */
class ValidateTrustedHost
{
    public function handle(Request $request, Closure $next)
    {
        if (app()->environment(['local', 'testing'])) {
            return $next($request);
        }

        $host = $request->getHost();

        if (in_array($host, (array) config('tenancy.central_domains', []), true)) {
            return $next($request);
        }

        if (! DB::table('domains')->where('domain', $host)->exists()) {
            abort(404);
        }

        return $next($request);
    }
}
