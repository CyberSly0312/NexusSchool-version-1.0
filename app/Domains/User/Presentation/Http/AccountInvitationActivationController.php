<?php

declare(strict_types=1);

namespace App\Domains\User\Presentation\Http;

use App\Domains\School\Models\School;
use App\Domains\User\Exceptions\AccountInvitationActivationException;
use App\Domains\User\Services\AccountInvitationActivationService;
use App\Models\UserInvitation;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Public, tenant-host-bound account activation surface.
 *
 * The host resolves the school through the same fail-closed tenant middleware
 * used by the School Gateway. The token is only a capability: the activation
 * service rechecks that it belongs to the resolved school inside the same
 * transaction that consumes it. This controller never authenticates the user.
 */
final class AccountInvitationActivationController
{
    public function __construct(
        private readonly AccountInvitationActivationService $activationService,
    ) {
    }

    public function show(string $token): View|Response
    {
        $school = $this->resolvedSchool();

        if (! $this->activationService->findUsableForSchool($token, $school) instanceof UserInvitation) {
            return $this->invalidResponse($school);
        }

        return view('account-activation.activate', [
            'school' => $school,
            'token' => $token,
        ]);
    }

    public function store(Request $request, string $token): View|Response
    {
        $school = $this->resolvedSchool();

        $validated = $request->validate([
            'password' => ['required', 'string', 'confirmed', 'min:8'],
        ]);

        try {
            $this->activationService->activate($token, $validated['password'], $school);
        } catch (AccountInvitationActivationException) {
            return $this->invalidResponse($school);
        }

        return view('account-activation.activated', [
            'school' => $school,
        ]);
    }

    private function resolvedSchool(): School
    {
        $school = tenant();

        if (! $school instanceof School) {
            abort(404);
        }

        return $school;
    }

    private function invalidResponse(School $school): Response
    {
        return response()->view('account-activation.invalid', [
            'school' => $school,
        ], 404);
    }
}
