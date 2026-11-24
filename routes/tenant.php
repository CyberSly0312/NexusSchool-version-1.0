<?php

declare(strict_types=1);

use App\Domains\School\Presentation\Http\SchoolAccessPortalController;
use App\Domains\School\Presentation\Http\SchoolAdminSetupController;
use App\Domains\User\Presentation\Http\AccountInvitationActivationController;
use App\Http\Controllers\Auth\SchoolGatewayAuthController;
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

    Route::get('/login', [SchoolGatewayAuthController::class, 'showLogin'])->name('school.gateway.login');
    Route::post('/login', [SchoolGatewayAuthController::class, 'login'])->middleware('throttle:20,1')->name('school.gateway.login.submit');
    Route::post('/logout', [SchoolGatewayAuthController::class, 'logout'])->name('school.gateway.logout');

    Route::get('/panel-select', [SchoolGatewayAuthController::class, 'selectPanel'])->name('school.gateway.select-panel');
    Route::post('/panel-select', [SchoolGatewayAuthController::class, 'enterPanel'])->name('school.gateway.enter-panel');
    Route::get('/no-panel-access', [SchoolGatewayAuthController::class, 'selectPanel'])->name('school.gateway.no-access');

    /*
     * Public account activation. The host is authoritative: the invitation
     * token must belong to the school resolved by InitializeTenancyForPanel,
     * and the activation service rechecks that binding inside its transaction.
     * No authentication, auto-login or token issuance occurs here.
     */
    Route::get('/account/activate/{token}', [AccountInvitationActivationController::class, 'show'])
        ->middleware('throttle:invitation-activation')
        ->name('school.account.activation.show');

    Route::post('/account/activate/{token}', [AccountInvitationActivationController::class, 'store'])
        ->middleware('throttle:invitation-activation')
        ->name('school.account.activation.store');
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
