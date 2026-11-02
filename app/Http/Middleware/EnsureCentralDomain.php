<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Restricts central-only interfaces to configured platform hosts.
 *
 * This is intentionally separate from ValidateTrustedHost: that middleware
 * validates hosts permitted to reach the application generally, while this
 * middleware enforces the narrower Super Admin panel boundary. getHost()
 * honors forwarded host data only after Laravel's trusted-proxy handling.
 */
class EnsureCentralDomain
{
    public function handle(Request $request, Closure $next)
    {
        $configuredDomains = config('tenancy.central_domains');

        if (! is_array($configuredDomains)) {
            abort(404);
        }

        $centralDomains = array_values(array_unique(array_filter(array_map(
            static fn (mixed $domain): ?string => is_string($domain) && filled(trim($domain))
                ? strtolower(trim($domain))
                : null,
            $configuredDomains,
        ))));

        if ($centralDomains === [] || ! in_array(strtolower($request->getHost()), $centralDomains, true)) {
            abort(404);
        }

        return $next($request);
    }
}
