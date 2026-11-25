<?php

declare(strict_types=1);

namespace App\Domains\AccountRecovery\Presentation\Http;

use App\Domains\AccountRecovery\DTOs\CompletePasswordResetCommand;
use App\Domains\AccountRecovery\Enums\PasswordResetFailure;
use App\Domains\AccountRecovery\Exceptions\PasswordResetException;
use App\Domains\AccountRecovery\Services\SchoolAdminPasswordResetService;
use App\Domains\School\Models\School;
use App\Models\UserInvitation;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tenant-host-bound page where a School Admin defines their own new password
 * after a Super Admin initiated recovery. The page never displays, accepts or
 * transmits a password chosen by somebody else.
 */
final class SchoolAdminPasswordResetController
{
    public function __construct(
        private readonly SchoolAdminPasswordResetService $resetService,
    ) {
    }

    public function show(string $token): View|Response
    {
        $school = $this->resolvedSchool();

        if (! $this->resetService->findResetForSchool($token, $school) instanceof UserInvitation) {
            return $this->invalidResponse($school);
        }

        return view('account-recovery.password-reset.reset', [
            'school' => $school,
            'token' => $token,
        ]);
    }

    public function store(Request $request, string $token): View|Response|RedirectResponse
    {
        $school = $this->resolvedSchool();

        if (! $this->resetService->findResetForSchool($token, $school) instanceof UserInvitation) {
            return $this->invalidResponse($school);
        }

        try {
            $this->resetService->complete(new CompletePasswordResetCommand(
                school: $school,
                token: $token,
                password: (string) $request->input('password', ''),
                passwordConfirmation: (string) $request->input('password_confirmation', ''),
            ));
        } catch (PasswordResetException $exception) {
            if ($exception->failure === PasswordResetFailure::Validation) {
                throw ValidationException::withMessages([
                    'password' => $exception->validationMessages,
                ]);
            }

            return $this->invalidResponse($school);
        }

        return redirect()->route('school.account.password-reset.done');
    }

    public function done(): View
    {
        return view('account-recovery.password-reset.done', [
            'school' => $this->resolvedSchool(),
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
        return response()->view('account-recovery.password-reset.invalid', [
            'school' => $school,
        ], 404);
    }
}
