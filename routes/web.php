<?php

declare(strict_types=1);

use App\Domains\AccountRecovery\Presentation\Http\CapabilityDeliveryController;
use App\Http\Middleware\EnsureCentralDomain;
use Illuminate\Support\Facades\Route;

// Tenant-facing root routing now lives in the tenant routes group
// (routes/tenant.php) — the School Access Portal is the only tenant-scoped
// root route. The central platform root has no route of its own.

/*
 * Central, Super Admin-only one-time handoff. The plaintext capability is
 * supplied in the URL fragment, which is never sent to the server; this route
 * therefore receives only the school/user identifiers and the purpose.
 */
Route::middleware(['web', EnsureCentralDomain::class, 'auth'])
    ->get('/admin/remise-lien/{school}/{user}', CapabilityDeliveryController::class)
    ->name('central.capability.delivery');
