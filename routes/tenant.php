<?php

declare(strict_types=1);

use App\Domains\School\Presentation\Http\SchoolAccessPortalController;
use App\Domains\School\Presentation\Http\SchoolAdminSetupController;
use App\Http\Middleware\InitializeTenancyForSchoolSetup;
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

/*
|--------------------------------------------------------------------------
| School Admin one-time setup
|--------------------------------------------------------------------------
|
| Reachable on the school's registered domain, including while the school is
| still `pending`. The hashed token is validated against the resolved tenant,
| the target user's school and the School Admin role; it is single-use and
| expires. No password is ever displayed or reset before redemption.
|
*/

Route::middleware([
    'web',
    InitializeTenancyForSchoolSetup::class,
    'throttle:30,1',
])->group(function () {
    Route::get('/school-admin/setup/done', [SchoolAdminSetupController::class, 'done'])
        ->name('school.admin.setup.done');

    Route::get('/school-admin/setup/{token}', [SchoolAdminSetupController::class, 'show'])
        ->name('school.admin.setup.show');

    Route::post('/school-admin/setup/{token}', [SchoolAdminSetupController::class, 'store'])
        ->name('school.admin.setup.store');
});
