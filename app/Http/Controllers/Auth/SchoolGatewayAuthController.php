<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Models\AuditLog;
use App\Models\User;
use App\Services\PanelAccessRegistry;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class SchoolGatewayAuthController
{
    public function __construct(
        private readonly PanelAccessRegistry $panelAccessRegistry,
    ) {
    }

    public function showLogin(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user instanceof User) {
            return $this->redirectAuthenticatedUser($request, $user);
        }

        return view('auth.school-gateway-login', [
            'school' => tenant(),
        ]);
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $this->ensureIsNotRateLimited($request, $credentials['email']);

        $normalizedEmail = Str::lower(trim($credentials['email']));

        $candidate = User::query()
            ->where('email', $normalizedEmail)
            ->where('school_id', tenant()?->getKey())
            ->first();

        if ($candidate instanceof User && ! $candidate->isActive()) {
            RateLimiter::hit($this->accountThrottleKey($request, $credentials['email']));
            $this->audit($request, 'auth.login_inactive_rejected', $candidate, 'Compte inactif refusé avant authentification.');

            throw ValidationException::withMessages([
                'email' => 'Ce compte n’est pas actif.',
            ]);
        }

        if (! Auth::guard('web')->attempt([
            'email' => $normalizedEmail,
            'password' => $credentials['password'],
        ], $request->boolean('remember'))) {
            RateLimiter::hit($this->ipThrottleKey($request));
            RateLimiter::hit($this->accountThrottleKey($request, $credentials['email']));

            $this->audit($request, 'auth.login_failed', null, 'Tentative de connexion à la passerelle scolaire refusée.', [
                'email' => $normalizedEmail,
            ]);

            throw ValidationException::withMessages([
                'email' => 'Ces identifiants ne correspondent pas à nos enregistrements.',
            ]);
        }

        RateLimiter::clear($this->ipThrottleKey($request));
        RateLimiter::clear($this->accountThrottleKey($request, $credentials['email']));

        $request->session()->regenerate();

        /** @var User $user */
        $user = Auth::guard('web')->user();

        $this->audit($request, 'auth.login_succeeded', $user, 'School gateway login succeeded.');

        return $this->redirectAuthenticatedUser($request, $user);
    }

    public function selectPanel(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if (! $user instanceof User) {
            return redirect()->route('school.gateway.login');
        }

        $panels = $this->panelAccessRegistry->authorizedPanelsFor($user);

        if ($panels === []) {
            $this->audit($request, 'auth.panel_access_denied', $user, 'Authenticated user has no authorized panel.');

            return view('auth.school-gateway-no-access', [
                'school' => tenant(),
            ]);
        }

        if (count($panels) === 1) {
            return redirect()->to($panels[0]['url']);
        }

        return view('auth.school-gateway-select-panel', [
            'school' => tenant(),
            'panels' => $panels,
        ]);
    }

    public function enterPanel(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (! $user instanceof User) {
            return redirect()->route('school.gateway.login');
        }

        $validated = $request->validate([
            'panel' => ['required', 'string'],
        ]);

        $panelId = $validated['panel'];

        if (! $this->panelAccessRegistry->isSelectablePanel($panelId) || ! $this->panelAccessRegistry->canUserAccessPanel($user, $panelId)) {
            $this->audit($request, 'auth.panel_selection_rejected', $user, 'Unauthorized panel selection rejected.', [
                'panel' => $panelId,
            ]);

            abort(403, 'You are not authorized to access the selected panel.');
        }

        $panel = \Filament\Facades\Filament::getPanels()[$panelId] ?? null;

        if (! $panel instanceof \Filament\Panel) {
            abort(404);
        }

        return redirect()->to($panel->getUrl());
    }

    public function logout(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user instanceof User) {
            $this->audit($request, 'auth.logout', $user, 'School gateway logout.');
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('school.gateway.login');
    }

    private function redirectAuthenticatedUser(Request $request, User $user): RedirectResponse
    {
        if (! $user->isActive()) {
            $this->audit($request, 'auth.panel_access_denied', $user, 'Inactive account denied during gateway routing.');
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'email' => 'Ce compte n’est pas actif.',
            ]);
        }

        $panels = $this->panelAccessRegistry->authorizedPanelsFor($user);

        if ($panels === []) {
            $this->audit($request, 'auth.panel_access_denied', $user, 'Authenticated user has no authorized panel.');

            return redirect()->route('school.gateway.no-access');
        }

        if (count($panels) === 1) {
            return redirect()->to($panels[0]['url']);
        }

        return redirect()->route('school.gateway.select-panel');
    }

    private function ensureIsNotRateLimited(Request $request, string $email): void
    {
        $ipKey = $this->ipThrottleKey($request);
        $accountKey = $this->accountThrottleKey($request, $email);

        if (! RateLimiter::tooManyAttempts($ipKey, 10) && ! RateLimiter::tooManyAttempts($accountKey, 5)) {
            return;
        }

        $seconds = max(
            RateLimiter::availableIn($ipKey),
            RateLimiter::availableIn($accountKey),
        );

        $this->audit($request, 'auth.login_throttled', null, 'School gateway login throttled.', [
            'email' => $email,
        ]);

        throw ValidationException::withMessages([
            'email' => __('Too many login attempts. Please try again in :seconds seconds.', ['seconds' => $seconds]),
        ]);
    }

    private function ipThrottleKey(Request $request): string
    {
        return 'school-gateway-login:ip:'.$request->ip();
    }

    private function accountThrottleKey(Request $request, string $email): string
    {
        $tenantId = (string) (tenant()?->getKey() ?? 'unknown-tenant');

        return 'school-gateway-login:account:'.$tenantId.':'.Str::lower($email);
    }

    /**
     * @param  array<string, mixed>  $metadata
     */
    private function audit(Request $request, string $action, ?User $user, string $description, array $metadata = []): void
    {
        try {
            AuditLog::query()->create([
                'school_id' => tenant()?->getKey(),
                'auditable_type' => $user ? $user->getMorphClass() : User::class,
                'auditable_id' => $user?->getKey(),
                'action' => $action,
                'old_values' => null,
                'new_values' => $metadata === [] ? null : $metadata,
                'event' => $action,
                'description' => $description,
                'ip_address' => $request->ip(),
                'user_agent' => Str::limit((string) $request->userAgent(), 512, ''),
                'tags' => 'auth,gateway',
                'user_id' => $user?->getKey(),
                'user_type' => $user?->getMorphClass(),
            ]);
        } catch (\Throwable) {
            // Authentication must not fail because audit persistence is unavailable.
        }
    }
}
