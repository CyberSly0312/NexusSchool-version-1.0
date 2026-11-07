<?php

declare(strict_types=1);

namespace App\Domains\School\Presentation\Http;

use App\Domains\School\Models\School;
use App\Domains\School\Models\SchoolAdminSetupToken;
use App\Domains\School\Services\SchoolAdminProvisioningService;
use App\Domains\User\Enums\UserStatus;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

/**
 * Tenant-domain, one-time School Admin setup page.
 *
 * The request host resolves the school through
 * {@see \App\Http\Middleware\InitializeTenancyForSchoolSetup}. A token is only
 * accepted when it is valid, unexpired, unused and belongs to the resolved
 * school and to a School Admin of that same school. Possession of the token is
 * the capability; the page never authenticates the visitor and never reveals an
 * existing password.
 */
final class SchoolAdminSetupController
{
    public function show(string $token): View
    {
        $setupToken = $this->resolveToken($token);
        $school = tenant();

        return view('school-admin.setup', [
            'school' => $school,
            'admin' => $setupToken->user,
            'token' => $token,
        ]);
    }

    public function store(Request $request, string $token): RedirectResponse
    {
        $this->resolveToken($token);

        $validated = $request->validate([
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(12)->letters()->mixedCase()->numbers()->symbols(),
            ],
        ]);

        app(SchoolAdminProvisioningService::class)->redeem($token, $validated['password']);

        return redirect()->route('school.admin.setup.done');
    }

    public function done(): View
    {
        $school = tenant();

        if (! $school instanceof School) {
            abort(404);
        }

        return view('school-admin.setup-done', [
            'school' => $school,
        ]);
    }

    private function resolveToken(string $token): SchoolAdminSetupToken
    {
        $school = tenant();

        if (! $school instanceof School) {
            abort(404);
        }

        $setupToken = app(SchoolAdminProvisioningService::class)->findValidByToken($token);

        if (! $setupToken instanceof SchoolAdminSetupToken) {
            abort(404);
        }

        if ((string) $setupToken->school_id !== (string) $school->getKey()) {
            abort(404);
        }

        if ($setupToken->user->status === UserStatus::Suspended) {
            abort(404);
        }

        return $setupToken;
    }
}
