<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Domains\School\Models\School;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Stancl\Tenancy\Facades\Tenancy;

/**
 * Domain-based tenant resolution for the school (tenant) panels.
 *
 * The canonical hostname registry is the stancl `domains` table: a request host
 * must match an explicit, registered domain row whose tenant is an existing,
 * non-deleted, active {@see School}. There is deliberately no fallback to the
 * authenticated user's school — the host is authoritative.
 *
 * Fail-closed rules:
 *  - central domains never serve a school panel (404);
 *  - unknown / unregistered hosts resolve to no tenant (404);
 *  - inactive schools are denied (403);
 *  - an authenticated user may only use their own school's panel (403);
 *  - guests reach the login screen of a valid school host.
 */
class InitializeTenancyForPanel
{
    public function handle(Request $request, Closure $next)
    {
        $host = $request->getHost();

        // The platform host must never expose a school panel.
        if (in_array($host, (array) config('tenancy.central_domains', []), true)) {
            abort(404);
        }

        // Resolve the tenant from the exact registered domain row.
        $tenant = School::query()
            ->whereHas('domains', fn ($query) => $query->where('domain', $host))
            ->first();

        // Unknown or unregistered hosts fail closed.
        if (! $tenant instanceof School) {
            abort(404);
        }

        // Only existing, non-deleted, active schools may serve a panel.
        if (! $tenant->isActive()) {
            abort(403);
        }

        // Establish the tenant context exactly once and never switch to a
        // different school mid-request.
        if (tenancy()->initialized) {
            if (tenancy()->tenant?->getKey() !== $tenant->getKey()) {
                abort(403);
            }
        } else {
            Tenancy::initialize($tenant);
        }

        // An authenticated user may only use the panel of the school they
        // belong to. Guests (login screen) have no tenant binding yet.
        $user = $request->user();

        if ($user instanceof User && $user->school_id !== $tenant->getKey()) {
            abort(403);
        }

        return $next($request);
    }
}
