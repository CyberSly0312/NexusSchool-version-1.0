<?php

declare(strict_types=1);

namespace App\Domains\AccountRecovery\Presentation\Http;

use App\Domains\School\Models\School;
use App\Domains\School\Services\SchoolAdminProvisioningService;
use App\Domains\User\Enums\UserStatus;
use App\Models\User;
use App\Models\UserInvitation;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Response;

/**
 * Central, Super Admin-only handoff of a one-time capability.
 *
 * The plaintext token is deliberately NOT sent to this page. It travels in the
 * URL fragment, which browsers never include in HTTP requests; the page script
 * reads it locally and immediately removes the fragment from browser history.
 * The server therefore never logs, persists or audits the plaintext token.
 */
final class CapabilityDeliveryController
{
    public function __invoke(Request $request, string $school, string $user): View|Response
    {
        $actor = auth()->user();

        if (! $actor instanceof User
            || $actor->trashed()
            || ! $actor->isActive()
            || ! $actor->hasRole('super_admin')) {
            abort(403);
        }

        /** @var School|null $schoolModel */
        $schoolModel = School::query()->find($school);

        if (! $schoolModel instanceof School || $schoolModel->trashed() || ! $schoolModel->isActive()) {
            abort(404);
        }

        $purpose = match ((string) $request->query('purpose', '')) {
            'reset' => UserInvitation::PURPOSE_SCHOOL_ADMIN_RESET,
            'activation' => UserInvitation::PURPOSE_SCHOOL_ADMIN_INITIAL,
            default => abort(404),
        };

        /** @var User|null $target */
        $target = User::query()
            ->whereKey($user)
            ->where('school_id', $schoolModel->getKey())
            ->first();

        $expectedStatus = $purpose === UserInvitation::PURPOSE_SCHOOL_ADMIN_RESET
            ? UserStatus::Active
            : UserStatus::Pending;

        if (! $target instanceof User
            || $target->trashed()
            || $target->status !== $expectedStatus
            || ! $target->hasRole(SchoolAdminProvisioningService::ROLE)) {
            abort(404);
        }

        /** @var UserInvitation|null $invitation */
        $invitation = UserInvitation::query()
            ->where('user_id', $target->getKey())
            ->where('school_id', $schoolModel->getKey())
            ->where('purpose', $purpose)
            ->whereNull('consumed_at')
            ->whereNull('revoked_at')
            ->where('expires_at', '>', now())
            ->latest('created_at')
            ->first();

        if (! $invitation instanceof UserInvitation) {
            return response()->view('central.capability-delivery-expired', [
                'school' => $schoolModel,
            ], 404);
        }

        $routeName = $purpose === UserInvitation::PURPOSE_SCHOOL_ADMIN_RESET
            ? 'school.account.password-reset.show'
            : 'school.account.activation.show';

        $domain = trim((string) ($schoolModel->domains()->value('domain') ?? $schoolModel->domain));

        return view('central.capability-delivery', [
            'school' => $schoolModel,
            'target' => $target,
            'purposeLabel' => $purpose === UserInvitation::PURPOSE_SCHOOL_ADMIN_RESET
                ? 'Réinitialisation du mot de passe'
                : 'Activation du compte',
            'templatePath' => route($routeName, ['token' => '__JETON__'], false),
            'schoolDomain' => $domain,
            'scheme' => request()->getScheme() ?: 'https',
            'expiresAt' => Carbon::parse((string) $invitation->expires_at)->format('d/m/Y H:i'),
        ]);
    }
}
