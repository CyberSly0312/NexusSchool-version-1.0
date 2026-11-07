<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Domains\School\Enums\SchoolStatus;
use App\Domains\School\Models\School;
use Closure;
use Illuminate\Http\Request;
use Stancl\Tenancy\Facades\Tenancy;

/**
 * Domain-based tenant resolution for the School Admin setup page.
 *
 * This is deliberately narrower than the general web routing: a brand-new
 * school is `pending` until its administrator completes setup, so the setup
 * page must be reachable for pending schools while the school panels remain
 * active-only. Suspended/inactive schools are still denied, central domains are
 * never tenant hosts, and unknown hosts fail closed.
 */
class InitializeTenancyForSchoolSetup
{
    public function handle(Request $request, Closure $next)
    {
        $host = $request->getHost();

        if (in_array($host, (array) config('tenancy.central_domains', []), true)) {
            abort(404);
        }

        $tenant = School::query()
            ->whereHas('domains', fn ($query) => $query->where('domain', $host))
            ->first();

        if (! $tenant instanceof School) {
            abort(404);
        }

        if (! in_array($tenant->status, [SchoolStatus::Pending, SchoolStatus::Active], true)) {
            abort(403);
        }

        if (tenancy()->initialized) {
            if (tenancy()->tenant?->getKey() !== $tenant->getKey()) {
                abort(403);
            }
        } else {
            Tenancy::initialize($tenant);
        }

        return $next($request);
    }
}
