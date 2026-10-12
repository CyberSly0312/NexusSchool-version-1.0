<?php

declare(strict_types=1);

namespace App\Domains\IdentityAndVerification;

use App\Domains\IdentityAndVerification\Application\Services\VerificationEligibility;
use App\Domains\IdentityAndVerification\Application\Services\VerificationIdentityService;
use App\Domains\IdentityAndVerification\Domain\Contracts\AcademicDocumentVerificationContract;
use App\Domains\IdentityAndVerification\Domain\Events\VerificationIdentityReplaced;
use App\Domains\IdentityAndVerification\Domain\Events\VerificationIdentityRevoked;
use App\Domains\IdentityAndVerification\Infrastructure\Contracts\AcademicDocumentVerificationAdapter;
use App\Domains\IdentityAndVerification\Infrastructure\Listeners\IdentityAndVerificationAuditLogger;
use App\Domains\IdentityAndVerification\Infrastructure\Models\VerificationIdentity;
use App\Domains\IdentityAndVerification\Infrastructure\Policies\VerificationIdentityPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

/**
 * Owning service provider for the Identity & Verification engine (SPR-016).
 *
 * Registers the verification identity service, the read-only
 * AcademicDocumentVerificationContract binding (ADR-022 C1), the named
 * rate limiter of the public verification surface, and — for Phase 4 — the
 * VerificationIdentityPolicy (document.revoke) plus the audit listeners that
 * write revocation / replacement events into the existing global AuditLog.
 * The public endpoint itself stays in the Presentation layer and is wired
 * through routes/verification.php; the surface deliberately ships no audit
 * UI, no QR codes and no offline (signed/JWT/HMAC) verification (ADR-022).
 */
final class IdentityAndVerificationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(VerificationIdentityService::class);
        $this->app->singleton(VerificationEligibility::class);
        $this->app->bind(AcademicDocumentVerificationContract::class, AcademicDocumentVerificationAdapter::class);
    }

    public function boot(): void
    {
        Gate::policy(VerificationIdentity::class, VerificationIdentityPolicy::class);

        Event::listen(VerificationIdentityRevoked::class, [IdentityAndVerificationAuditLogger::class, 'handleRevoked']);
        Event::listen(VerificationIdentityReplaced::class, [IdentityAndVerificationAuditLogger::class, 'handleReplaced']);

        RateLimiter::for('verification', function (Request $request): array {
            $token = (string) $request->route('token');

            return [
                Limit::perMinute(60)->by((string) $request->ip()),
                Limit::perMinute(10)->by('token:'.hash('sha256', $token)),
            ];
        });
    }
}
