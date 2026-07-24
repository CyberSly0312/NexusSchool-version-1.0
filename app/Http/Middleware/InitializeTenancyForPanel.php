<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Stancl\Tenancy\Facades\Tenancy;

class InitializeTenancyForPanel
{
    public function handle(Request $request, Closure $next)
    {
        if (tenancy()->initialized) {
            return $next($request);
        }

        $tenantId = Session::get('tenant_id');

        if ($tenantId) {
            $tenant = \App\Domains\School\Models\School::find($tenantId);

            if ($tenant) {
                Tenancy::initialize($tenant);
            }
        }

        return $next($request);
    }
}
