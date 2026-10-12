<?php

declare(strict_types=1);

use App\Domains\IdentityAndVerification\Presentation\Http\PublicVerificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Verification Routes
|--------------------------------------------------------------------------
|
| Central, unauthenticated, read-only verification surface (SPR-016 Phase 3,
| ADR-022 Decision 3). The token is the only lookup key and is never exposed
| back to the caller. The named limiter guards both the requester IP and the
| individual token digest.
|
*/

Route::get('/v/{token}', PublicVerificationController::class)
    ->name('verification.public')
    ->middleware('throttle:verification');
