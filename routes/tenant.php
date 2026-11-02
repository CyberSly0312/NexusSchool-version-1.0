<?php

declare(strict_types=1);

use App\Domains\School\Presentation\Http\SchoolAccessPortalController;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    //
});

/*
|--------------------------------------------------------------------------
| School Access Portal
|--------------------------------------------------------------------------
|
| Tenant-facing root route (`/`) rendering the School Access Portal for the
| resolved tenant. Uses the fail-closed tenant resolution middleware shared
| with the school panels (host → registered tenant → active school → tenant
| context). Central domains never resolve here; unknown hosts fail closed.
|
*/

Route::middleware([
    'web',
    \App\Http\Middleware\InitializeTenancyForPanel::class,
])->group(function () {
    Route::get('/', SchoolAccessPortalController::class)->name('school.portal');
});
