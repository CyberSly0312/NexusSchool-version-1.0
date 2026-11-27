<?php

declare(strict_types=1);

namespace App\Domains\User\Services;

use App\Domains\Enrollment\Domain\Enums\EnrollmentStatus;
use App\Domains\Enrollment\Infrastructure\Models\Enrollment;
use App\Domains\Guardian\Infrastructure\Models\Guardian;
use App\Domains\Guardian\Infrastructure\Models\StudentGuardianRelationship;
use App\Domains\IdentityMatching\Application\DTOs\IdentityMatchRequest;
use App\Domains\IdentityMatching\Application\DTOs\IdentityMatchResult;
use App\Domains\IdentityMatching\Application\Services\SchoolScopedIdentityMatcher;
use App\Domains\IdentityMatching\Domain\Enums\IdentityMatchOutcome;
use App\Domains\IdentityMatching\Domain\Enums\MatchableEntityType;
use App\Domains\School\Models\School;
use App\Domains\Student\Infrastructure\Models\Student;
use App\Domains\System\Enums\AuditAction;
use App\Domains\System\Services\AuditService;
use App\Domains\User\DTOs\AccountProvisioningCommand;
use App\Domains\User\DTOs\AccountProvisioningContext;
use App\Domains\User\DTOs\EnrollmentInvitationDescriptor;
use App\Domains\User\DTOs\EnrollmentProvisioningRequest;
use App\Domains\User\DTOs\EnrollmentProvisioningResult;
use App\Domains\User\Enums\EnrollmentInvitationLifecycle;
use App\Domains\User\Enums\EnrollmentProvisioningKind;
use App\Domains\User\Enums\EnrollmentProvisioningRejectionCode;
use App\Domains\User\Exceptions\AccountProvisioningException;
use App\Domains\User\Exceptions\EnrollmentProvisioningException;
use App\Domains\User\Policies\EnrollmentLinkedProvisioningPolicy;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Narrow Task 11.2 provisioning foundation.
 *
 * It creates or reuses a pending/eligible authentication User only after a
 * confirmed same-school enrollment, an explicit trusted request and exact
 * Task 11.1 identity resolution. It never links a child, activates an account,
 * assigns a role through generic role management, delivers a token or exposes
 * student data. Parent/student activation remains blocked pending Task 11.3.
 */
final class EnrollmentLinkedAccountProvisioningService
{
    public function __construct(
        private readonly AccountProvisioningService $accountProvisioning = new AccountProvisioningService(),
        private readonly SchoolScopedIdentityMatcher $matcher = new SchoolScopedIdentityMatcher(),
        private readonly EnrollmentLinkedInvitationService $invitations = new EnrollmentLinkedInvitationService(),
        private readonly AuditService $audit = new AuditService(),
    ) {
    }

    public function provision(EnrollmentProvisioningRequest $request): EnrollmentProvisioningResult
    {
        try {
            $school = $this->resolveTrustedSchool($request);
            $actor = $this->resolveActor($request->actor, (string) $school->getKey());
            $this->auditRequested($request, $actor, $school);

            [$enrollment, $student] = $this->resolveEnrollment($request, $school);
            $matchReasons = [];

            $outcome = DB::transaction(function () use (
                $request,
                $school,
                $actor,
                $student,
                &$matchReasons,
            ): array {
                $user = $request->kind === EnrollmentProvisioningKind::Student
                    ? $this->provisionStudentUser($request, $school, $actor, $student, $matchReasons)
                    : $this->provisionParentUser($request, $school, $actor, $student, $matchReasons);

                $invitation = $this->invitations->prepare($request->kind, $user, $actor);

                return [$user, $invitation];
            });

            /** @var User $user */
            [$user, $invitation] = $outcome;
            /** @var EnrollmentInvitationDescriptor $invitation */

            $result = new EnrollmentProvisioningResult(
                user: $user->refresh(),
                kind: $request->kind,
                userCreated: $user->wasRecentlyCreated,
                enrollmentId: (string) $enrollment->getKey(),
                studentId: (string) $student->getKey(),
                guardianId: $request->kind === EnrollmentProvisioningKind::Parent
                    ? (string) $request->parentRequest?->guardianId
                    : null,
                invitation: $invitation,
                deliveryBlockReason: EnrollmentProvisioningRejectionCode::ActivationBlocked,
                matchReasonCodes: $this->reasonCodes($matchReasons),
            );

            $this->auditSuccess($request, $result, $actor, $school);

            return $result;
        } catch (EnrollmentProvisioningException $exception) {
            $this->auditRejection($request, $exception->reasonCode);

            throw $exception;
        }
    }

    private function resolveTrustedSchool(EnrollmentProvisioningRequest $request): School
    {
        $tenant = tenant();

        if (! $tenant instanceof School) {
            throw EnrollmentProvisioningException::for(
                EnrollmentProvisioningRejectionCode::TenantContextMissing,
                'Le contexte d’établissement est requis.',
            );
        }

        $tenantId = (string) $tenant->getKey();
        $requestedSchoolId = trim($request->schoolId);

        if ($tenantId === '' || $requestedSchoolId === '' || $tenantId !== $requestedSchoolId) {
            throw EnrollmentProvisioningException::for(
                EnrollmentProvisioningRejectionCode::TenantContextMismatch,
                'L’établissement demandé ne correspond pas au contexte courant.',
            );
        }

        if ($tenant->trashed() || ! $tenant->isActive()) {
            throw EnrollmentProvisioningException::for(
                EnrollmentProvisioningRejectionCode::TenantSchoolInactive,
                'L’établissement courant n’est pas disponible.',
            );
        }

        return $tenant;
    }

    private function resolveActor(User $actor, string $schoolId): User
    {
        /** @var User|null $persisted */
        $persisted = User::query()->whereKey($actor->getKey())->first();

        if (! $persisted instanceof User || $persisted->trashed() || ! $persisted->isActive()) {
            throw EnrollmentProvisioningException::for(
                EnrollmentProvisioningRejectionCode::ActorInactive,
                'Seul un administrateur actif peut lancer ce provisionnement.',
            );
        }

        if (! $persisted->hasRole('school_admin') || (string) $persisted->school_id !== $schoolId) {
            throw EnrollmentProvisioningException::for(
                EnrollmentProvisioningRejectionCode::ActorNotAuthorized,
                'Cet acteur n’est pas autorisé pour ce provisionnement lié à une inscription.',
            );
        }

        return $persisted;
    }

    /**
     * @return array{0: Enrollment, 1: Student}
     */
    private function resolveEnrollment(
        EnrollmentProvisioningRequest $request,
        School $school,
    ): array {
        $schoolId = (string) $school->getKey();

        /** @var Enrollment|null $enrollment */
        $enrollment = Enrollment::query()
            ->whereKey($request->enrollmentId)
            ->where('school_id', $schoolId)
            ->where('status', EnrollmentStatus::Active->value)
            ->first();

        if (! $enrollment instanceof Enrollment) {
            throw EnrollmentProvisioningException::for(
                EnrollmentProvisioningRejectionCode::EnrollmentNotConfirmed,
                'Aucune inscription confirmée n’a été trouvée pour cette opération.',
            );
        }

        $student = $enrollment->student;
        if (! $student instanceof Student
            || $student->trashed()
            || (string) $student->school_id !== $schoolId) {
            throw EnrollmentProvisioningException::for(
                EnrollmentProvisioningRejectionCode::EnrollmentMismatch,
                'L’élève de l’inscription n’appartient pas à l’établissement courant.',
            );
        }

        return [$enrollment, $student];
    }

    /**
     * @param  list<IdentityMatchResult>  $matchReasons
     */
    private function provisionStudentUser(
        EnrollmentProvisioningRequest $request,
        School $school,
        User $actor,
        Student $student,
        array &$matchReasons,
    ): User {
        if (! $request->explicitSchoolRequest) {
            throw EnrollmentProvisioningException::for(
                EnrollmentProvisioningRejectionCode::ExplicitRequestMissing,
                'Un compte élève exige une demande explicite de l’établissement.',
            );
        }

        if (! $request->studentEvidence?->isComplete()) {
            throw EnrollmentProvisioningException::for(
                $request->studentEvidence !== null && ! $request->studentEvidence->contactEvidenceConfirmed
                    ? EnrollmentProvisioningRejectionCode::ContactEvidenceMissing
                    : EnrollmentProvisioningRejectionCode::EligibilityMissing,
                'Les justificatifs d’éligibilité et de contact requis sont incomplets.',
            );
        }

        $schoolId = (string) $school->getKey();
        $studentMatch = $this->matcher->match(new IdentityMatchRequest(
            entityType: MatchableEntityType::Student,
            schoolId: $schoolId,
            email: $request->email,
            phone: $request->phone,
            firstName: $request->firstName,
            lastName: $request->lastName,
            studentId: $student->getKey(),
            studentNumber: $student->getAttribute('student_number'),
            admissionNumber: $student->getAttribute('admission_number'),
        ));
        $matchReasons[] = $studentMatch;

        $this->assertExactMatch(
            $studentMatch,
            $student->getKey(),
            EnrollmentProvisioningRejectionCode::IdentityPossible,
        );

        $studentUserId = $student->getAttribute('user_id');
        if ($studentUserId !== null && (string) $studentUserId !== '') {
            $userMatch = $this->matcher->match(new IdentityMatchRequest(
                entityType: MatchableEntityType::User,
                schoolId: $schoolId,
                email: $request->email,
                phone: $request->phone,
                firstName: $request->firstName,
                lastName: $request->lastName,
                userId: $studentUserId,
                expectedRole: EnrollmentLinkedProvisioningPolicy::roleFor(EnrollmentProvisioningKind::Student),
            ));
            $matchReasons[] = $userMatch;

            $this->assertExactMatch(
                $userMatch,
                $studentUserId,
                EnrollmentProvisioningRejectionCode::IdentityPossible,
            );

            /** @var User $existing */
            $existing = User::query()->whereKey($studentUserId)->firstOrFail();

            return $existing;
        }

        return $this->createPendingUser($request, $school, $actor, EnrollmentProvisioningKind::Student);
    }

    /**
     * @param  list<IdentityMatchResult>  $matchReasons
     */
    private function provisionParentUser(
        EnrollmentProvisioningRequest $request,
        School $school,
        User $actor,
        Student $student,
        array &$matchReasons,
    ): User {
        if (! $request->explicitSchoolRequest || ! $request->parentRequest?->isComplete()) {
            throw EnrollmentProvisioningException::for(
                EnrollmentProvisioningRejectionCode::ExplicitRequestMissing,
                'Une invitation parent exige une demande explicite pour un tuteur précis.',
            );
        }

        $schoolId = (string) $school->getKey();

        /** @var Guardian|null $guardian */
        $guardian = Guardian::query()
            ->forSchool($schoolId)
            ->whereKey($request->parentRequest->guardianId)
            ->first();

        if (! $guardian instanceof Guardian || $guardian->trashed()) {
            throw EnrollmentProvisioningException::for(
                EnrollmentProvisioningRejectionCode::GuardianNotSelected,
                'Le tuteur sélectionné n’est pas disponible dans cet établissement.',
            );
        }

        $durableRelationship = StudentGuardianRelationship::query()
            ->forSchool($schoolId)
            ->where('student_id', $student->getKey())
            ->where('guardian_id', $guardian->getKey())
            ->exists();

        if (! $durableRelationship) {
            throw EnrollmentProvisioningException::for(
                EnrollmentProvisioningRejectionCode::GuardianNotEnrollmentLinked,
                'Le tuteur sélectionné n’a pas de relation durable avec cet élève.',
            );
        }

        $guardianMatch = $this->matcher->match(new IdentityMatchRequest(
            entityType: MatchableEntityType::Guardian,
            schoolId: $schoolId,
            email: $request->email,
            phone: $request->phone,
            firstName: $request->firstName,
            lastName: $request->lastName,
            guardianId: $guardian->getKey(),
            guardianNumber: $guardian->getAttribute('guardian_number'),
            studentId: $student->getKey(),
        ));
        $matchReasons[] = $guardianMatch;

        $this->assertExactMatch(
            $guardianMatch,
            $guardian->getKey(),
            EnrollmentProvisioningRejectionCode::IdentityPossible,
        );

        $guardianUserId = $guardian->getAttribute('user_id');
        if ($guardianUserId !== null && (string) $guardianUserId !== '') {
            $userMatch = $this->matcher->match(new IdentityMatchRequest(
                entityType: MatchableEntityType::User,
                schoolId: $schoolId,
                email: $request->email,
                phone: $request->phone,
                firstName: $request->firstName,
                lastName: $request->lastName,
                guardianId: $guardian->getKey(),
                studentId: $student->getKey(),
                sameSchoolCorroboration: true,
                expectedRole: EnrollmentLinkedProvisioningPolicy::roleFor(EnrollmentProvisioningKind::Parent),
            ));
            $matchReasons[] = $userMatch;

            $this->assertExactMatch(
                $userMatch,
                $guardianUserId,
                EnrollmentProvisioningRejectionCode::IdentityPossible,
            );

            /** @var User $existing */
            $existing = User::query()->whereKey($guardianUserId)->firstOrFail();

            return $existing;
        }

        return $this->createPendingUser($request, $school, $actor, EnrollmentProvisioningKind::Parent);
    }

    private function createPendingUser(
        EnrollmentProvisioningRequest $request,
        School $school,
        User $actor,
        EnrollmentProvisioningKind $kind,
    ): User {
        $roleName = EnrollmentLinkedProvisioningPolicy::roleFor($kind);

        try {
            return $this->accountProvisioning->provisionEnrollmentLinkedAccount(
                new AccountProvisioningCommand(
                    schoolId: (string) $school->getKey(),
                    firstName: $request->firstName,
                    lastName: $request->lastName,
                    email: $request->email,
                    roleName: $roleName,
                    phone: $request->phone,
                ),
                AccountProvisioningContext::forSchoolAdmin($actor),
                $kind,
            );
        } catch (AccountProvisioningException $exception) {
            $code = match ($exception->reason) {
                AccountProvisioningException::REASON_EMAIL => str_contains($exception->getMessage(), 'required')
                    ? EnrollmentProvisioningRejectionCode::EmailRequired
                    : EnrollmentProvisioningRejectionCode::EmailUnavailable,
                AccountProvisioningException::REASON_ROLE => EnrollmentProvisioningRejectionCode::RoleUnavailable,
                AccountProvisioningException::REASON_ACTOR => EnrollmentProvisioningRejectionCode::ActorInactive,
                default => EnrollmentProvisioningRejectionCode::TenantContextMismatch,
            };

            throw EnrollmentProvisioningException::for($code, $exception->getMessage());
        }
    }

    private function assertExactMatch(
        IdentityMatchResult $result,
        int|string $expectedId,
        EnrollmentProvisioningRejectionCode $possibleCode,
    ): void {
        if ($result->outcome === IdentityMatchOutcome::ExactMatch
            && (string) $result->candidateId === (string) $expectedId) {
            return;
        }

        if ($result->outcome === IdentityMatchOutcome::AmbiguousOrConflict) {
            throw EnrollmentProvisioningException::for(
                EnrollmentProvisioningRejectionCode::IdentityConflict,
                'L’identité est ambiguë ou en conflit; aucun compte n’est créé ni réutilisé.',
            );
        }

        if ($result->outcome === IdentityMatchOutcome::PossibleMatch) {
            throw EnrollmentProvisioningException::for(
                $possibleCode,
                'L’identité n’est pas prouvée; aucun compte n’est créé ni réutilisé.',
            );
        }

        throw EnrollmentProvisioningException::for(
            EnrollmentProvisioningRejectionCode::IdentityCandidateMismatch,
            'Le candidat d’identité ne correspond pas à la cible autorisée.',
        );
    }

    /**
     * @param  list<IdentityMatchResult>  $results
     * @return list<string>
     */
    private function reasonCodes(array $results): array
    {
        $codes = [];

        foreach ($results as $result) {
            foreach ($result->reasonCodes as $reason) {
                $codes[$reason->value] = $reason->value;
            }
        }

        return array_values($codes);
    }

    /**
     * @return array<string, string|int|null>
     */
    private function commonAuditValues(
        EnrollmentProvisioningRequest $request,
        ?User $actor,
        School $school,
    ): array {
        return [
            'kind' => $request->kind->value,
            'enrollment_id' => $request->enrollmentId,
            'student_id' => $request->kind === EnrollmentProvisioningKind::Parent
                ? null
                : $this->studentIdForEnrollment($request->enrollmentId),
            'guardian_id' => $request->parentRequest !== null
                ? (string) $request->parentRequest->guardianId
                : null,
            'actor_id' => $actor !== null ? (string) $actor->getKey() : (string) $request->actor->getKey(),
            'school_id' => (string) $school->getKey(),
        ];
    }

    private function studentIdForEnrollment(string $enrollmentId): ?string
    {
        /** @var Enrollment|null $enrollment */
        $enrollment = Enrollment::query()->whereKey($enrollmentId)->first();

        return $enrollment?->student_id !== null ? (string) $enrollment->student_id : null;
    }

    private function auditRequested(
        EnrollmentProvisioningRequest $request,
        User $actor,
        School $school,
    ): void {
        $event = $request->kind === EnrollmentProvisioningKind::Student
            ? 'student_account.provisioning_requested'
            : 'guardian_portal.invitation_requested';

        $this->audit->record([
            'school_id' => $school->getKey(),
            'auditable_type' => User::class,
            'auditable_id' => (string) $actor->getKey(),
            'action' => AuditAction::ProvisioningRequested->value,
            'event' => $event,
            'description' => $request->kind === EnrollmentProvisioningKind::Student
                ? 'Demande explicite de provisionnement d’un compte élève enregistrée.'
                : 'Demande explicite d’invitation au portail parent enregistrée.',
            'new_values' => $this->commonAuditValues($request, $actor, $school),
            'user_id' => (string) $actor->getKey(),
            'user_type' => User::class,
        ]);
    }

    private function auditSuccess(
        EnrollmentProvisioningRequest $request,
        EnrollmentProvisioningResult $result,
        User $actor,
        School $school,
    ): void {
        $newValues = $this->commonAuditValues($request, $actor, $school);
        $newValues['student_id'] = $result->studentId;
        $newValues['guardian_id'] = $result->guardianId;
        $newValues['user_id'] = (string) $result->user->getKey();
        $newValues['user_created'] = $result->userCreated;
        $newValues['match_reason_codes'] = implode(',', $result->matchReasonCodes);

        if ($result->invitation !== null) {
            $newValues['invitation_id'] = $result->invitation->invitationId;
            $newValues['invitation_purpose'] = $result->invitation->purpose;
            $newValues['invitation_lifecycle'] = $result->invitation->lifecycle->value;
        }

        $this->audit->record([
            'school_id' => $school->getKey(),
            'auditable_type' => User::class,
            'auditable_id' => (string) $result->user->getKey(),
            'action' => AuditAction::Provisioned->value,
            'event' => $result->kind === EnrollmentProvisioningKind::Student
                ? 'student_account.provisioned'
                : 'guardian_portal.invitation_prepared',
            'description' => 'Provisionnement lié à une inscription terminé sans activation ni accès aux dossiers d’élèves.',
            'new_values' => $newValues,
            'user_id' => (string) $actor->getKey(),
            'user_type' => User::class,
        ]);

        if ($result->invitation !== null) {
            $action = match ($result->invitation->lifecycle) {
                EnrollmentInvitationLifecycle::Reused => AuditAction::InvitationReused,
                EnrollmentInvitationLifecycle::Reissued => AuditAction::InvitationReissued,
                EnrollmentInvitationLifecycle::Issued => AuditAction::SetupLinkIssued,
            };

            $this->audit->record([
                'school_id' => $school->getKey(),
                'auditable_type' => User::class,
                'auditable_id' => (string) $result->user->getKey(),
                'action' => $action->value,
                'event' => 'enrollment_provisioning.invitation.'.$result->invitation->lifecycle->value,
                'description' => 'Cycle de vie de l’invitation enregistré sans jeton ni secret.',
                'new_values' => [
                    'invitation_id' => $result->invitation->invitationId,
                    'invitation_purpose' => $result->invitation->purpose,
                    'enrollment_id' => $result->enrollmentId,
                    'user_id' => (string) $result->user->getKey(),
                ],
                'user_id' => (string) $actor->getKey(),
                'user_type' => User::class,
            ]);
        }

        $this->audit->record([
            'school_id' => $school->getKey(),
            'auditable_type' => User::class,
            'auditable_id' => (string) $result->user->getKey(),
            'action' => AuditAction::ProvisioningBlocked->value,
            'event' => 'family_panel_activation.deferred_to_task_11_3',
            'description' => 'Aucune activation ni accès aux dossiers d’élèves n’est autorisé avant l’autorisation parent/élève par relation.',
            'new_values' => [
                'reason_code' => $result->deliveryBlockReason->value,
                'enrollment_id' => $result->enrollmentId,
                'user_id' => (string) $result->user->getKey(),
            ],
            'user_id' => (string) $actor->getKey(),
            'user_type' => User::class,
        ]);
    }

    private function auditRejection(
        EnrollmentProvisioningRequest $request,
        EnrollmentProvisioningRejectionCode $reasonCode,
    ): void {
        $tenant = tenant();
        $schoolId = $tenant instanceof School ? (string) $tenant->getKey() : null;

        $this->audit->record([
            'school_id' => $schoolId,
            'auditable_type' => User::class,
            'auditable_id' => (string) $request->actor->getKey(),
            'action' => AuditAction::ProvisioningBlocked->value,
            'event' => 'enrollment_provisioning.rejected',
            'description' => 'Provisionnement lié à une inscription refusé par une règle de sécurité.',
            'new_values' => [
                'reason_code' => $reasonCode->value,
                'kind' => $request->kind->value,
                'enrollment_id' => $request->enrollmentId,
                'tenant_school_id' => $schoolId,
            ],
            'user_id' => (string) $request->actor->getKey(),
            'user_type' => User::class,
        ]);
    }
}
