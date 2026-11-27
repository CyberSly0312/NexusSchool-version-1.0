<?php

declare(strict_types=1);

namespace App\Domains\User\Services;

use App\Domains\User\DTOs\EnrollmentInvitationDescriptor;
use App\Domains\User\Enums\EnrollmentInvitationLifecycle;
use App\Domains\User\Enums\EnrollmentProvisioningKind;
use App\Domains\User\Enums\EnrollmentProvisioningRejectionCode;
use App\Domains\User\Exceptions\EnrollmentProvisioningException;
use App\Domains\User\Policies\EnrollmentLinkedProvisioningPolicy;
use App\Models\User;
use App\Models\UserInvitation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Invitation lifecycle for the narrow Task 11.2 provisioning foundation.
 *
 * All purposes are server-derived from the approved provisioning kind. The
 * service only stores hash-only invitations through UserInvitation::issue()
 * and returns a non-secret descriptor. Plaintext tokens are discarded and are
 * never logged, audited or delivered by this slice.
 */
final class EnrollmentLinkedInvitationService
{
    public function prepare(
        EnrollmentProvisioningKind $kind,
        User $user,
        User $actor,
    ): EnrollmentInvitationDescriptor {
        $schoolId = (string) $user->school_id;
        if ($schoolId === '') {
            throw EnrollmentProvisioningException::for(
                EnrollmentProvisioningRejectionCode::TenantContextMissing,
                'L’utilisateur cible n’appartient à aucun établissement.',
            );
        }

        $purpose = EnrollmentLinkedProvisioningPolicy::purposeFor($kind);

        return DB::transaction(function () use ($purpose, $user, $actor, $schoolId): EnrollmentInvitationDescriptor {
            $now = Carbon::now();

            /** @var list<UserInvitation> $outstanding */
            $outstanding = UserInvitation::query()
                ->where('user_id', $user->getKey())
                ->where('school_id', $schoolId)
                ->whereNull('consumed_at')
                ->whereNull('revoked_at')
                ->lockForUpdate()
                ->get()
                ->all();

            $samePurposeUsable = array_values(array_filter(
                $outstanding,
                static fn (UserInvitation $invitation): bool => $invitation->purpose === $purpose
                    && ! $invitation->isExpired($now),
            ));

            if ($samePurposeUsable !== []) {
                $reusable = array_shift($samePurposeUsable);

                foreach ($samePurposeUsable as $duplicate) {
                    $duplicate->forceFill(['revoked_at' => $now])->save();
                }

                $this->assertSingleUsableInvitation($user, $schoolId, $purpose, $now);

                return $this->describe(
                    $reusable,
                    EnrollmentInvitationLifecycle::Reused,
                    $now,
                );
            }

            $superseded = array_filter(
                $outstanding,
                static fn (UserInvitation $invitation): bool => ! $invitation->isExpired($now)
                    || $invitation->purpose === $purpose,
            );

            foreach ($superseded as $invitation) {
                $invitation->forceFill(['revoked_at' => $now])->save();
            }

            $issue = UserInvitation::issue(
                user: $user,
                purpose: $purpose,
                invitedBy: $actor,
            );

            $invitation = $issue->invitation;
            $this->assertSingleUsableInvitation($user, $schoolId, $purpose, $now);

            return $this->describe(
                $invitation,
                $superseded === [] ? EnrollmentInvitationLifecycle::Issued : EnrollmentInvitationLifecycle::Reissued,
                $now,
            );
        });
    }

    private function describe(
        UserInvitation $invitation,
        EnrollmentInvitationLifecycle $lifecycle,
        Carbon $now,
    ): EnrollmentInvitationDescriptor {
        $expiresAt = $invitation->expires_at;

        return new EnrollmentInvitationDescriptor(
            invitationId: (string) $invitation->getKey(),
            purpose: (string) $invitation->purpose,
            lifecycle: $lifecycle,
            expiresAt: Carbon::parse((string) $expiresAt)->toIso8601String(),
            usable: $invitation->isUsable($now),
        );
    }

    private function assertSingleUsableInvitation(
        User $user,
        string $schoolId,
        string $purpose,
        Carbon $now,
    ): void {
        $count = UserInvitation::query()
            ->where('user_id', $user->getKey())
            ->where('school_id', $schoolId)
            ->where('purpose', $purpose)
            ->whereNull('consumed_at')
            ->whereNull('revoked_at')
            ->where('expires_at', '>', $now)
            ->count();

        if ($count !== 1) {
            throw EnrollmentProvisioningException::for(
                EnrollmentProvisioningRejectionCode::InvitationConflict,
                'État d’invitation incohérent: plusieurs invitations utilisables.',
            );
        }
    }
}
