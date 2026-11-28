<?php

declare(strict_types=1);

namespace App\Domains\Guardian\Application\Services;

use App\Domains\Guardian\Application\DTOs\GuardianRelationshipRevocationData;
use App\Domains\Guardian\Application\DTOs\GuardianRelationshipVerificationData;
use App\Domains\Guardian\Domain\Enums\GuardianAccessDenialReason;
use App\Domains\Guardian\Domain\Enums\RelationshipPortalAccessStatus;
use App\Domains\Guardian\Domain\Enums\RelationshipVerificationStatus;
use App\Domains\Guardian\Domain\Exceptions\GuardianRelationshipVerificationException;
use App\Domains\Guardian\Infrastructure\Models\Guardian;
use App\Domains\Guardian\Infrastructure\Models\StudentGuardianRelationship;
use App\Domains\Guardian\Infrastructure\Models\StudentGuardianRelationshipHistory;
use App\Domains\IdentityMatching\Application\DTOs\IdentityMatchRequest;
use App\Domains\IdentityMatching\Application\DTOs\IdentityMatchResult;
use App\Domains\IdentityMatching\Application\Services\SchoolScopedIdentityMatcher;
use App\Domains\IdentityMatching\Domain\Enums\IdentityMatchOutcome;
use App\Domains\IdentityMatching\Domain\Enums\MatchableEntityType;
use App\Domains\School\Models\School;
use App\Domains\Student\Infrastructure\Models\Student;
use App\Domains\System\Enums\AuditAction;
use App\Domains\System\Services\AuditService;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

/**
 * Authoritative lifecycle for Guardian–Student portal access.
 *
 * Identity matching and relationship proof are separate. Task 11.1 matching is
 * used only to confirm that the target Parent account and Guardian record are
 * the same identity. Portal authorization additionally requires an existing
 * durable relationship created by the school-scoped link process, an explicit
 * request, an authorized staff reviewer, and a distinct authorized actor for
 * revocation. Verification never creates a relationship.
 */
final class GuardianRelationshipVerificationService
{
    public function __construct(
        private readonly SchoolScopedIdentityMatcher $matcher = new SchoolScopedIdentityMatcher(),
        private readonly AuditService $audit = new AuditService(),
    ) {
    }

    /**
     * Register a review request. A request never grants portal access.
     */
    public function request(
        GuardianRelationshipVerificationData $data,
        ?User $actor = null,
    ): StudentGuardianRelationship {
        $school = $this->resolveSchool();
        $actor = $this->resolveActor($actor, $school, $data);
        $relationship = $this->resolveRelationship($data->relationshipId, $school, $data, $actor);

        $this->authorizeActor($actor, $relationship, 'verify', $data);
        $this->assertNoRelationshipConflict($relationship, $data, $actor);
        [$guardian] = $this->assertSubjectOwnership($relationship, $school, $data, $actor);
        $this->assertTargetSeparation($actor, $guardian, $data, $relationship);

        if ($relationship->verification_status !== RelationshipVerificationStatus::Unverified
            || $relationship->portal_access_status !== RelationshipPortalAccessStatus::NotAuthorized) {
            $this->deny(
                GuardianAccessDenialReason::RelationshipStateConflict,
                $data,
                $actor,
                $relationship,
            );
        }

        if (! $this->requestExists($relationship)) {
            $this->recordHistory(
                $relationship,
                'portal_access_requested',
                'Portal access review requested by authorized school actor',
                $actor,
                ['reason_code' => 'REQUESTED'],
            );

            $this->auditRelationshipAction(
                AuditAction::GuardianRelationshipRequested,
                $actor,
                ['reason_code' => 'REQUESTED'],
                $relationship,
            );
        }

        return $relationship->refresh();
    }

    /**
     * Approve an existing requested relationship after trusted provenance and
     * identity checks. This method never creates a relationship.
     */
    public function verifyAndAuthorize(
        GuardianRelationshipVerificationData $data,
        ?User $actor = null,
    ): StudentGuardianRelationship {
        $school = $this->resolveSchool();
        $actor = $this->resolveActor($actor, $school, $data);
        $relationship = $this->resolveRelationship($data->relationshipId, $school, $data, $actor);

        $this->authorizeActor($actor, $relationship, 'verify', $data);
        $this->assertNoRelationshipConflict($relationship, $data, $actor);
        [$guardian, $student, $parentUser] = $this->assertSubjectOwnership($relationship, $school, $data, $actor);
        $this->assertTargetSeparation($actor, $guardian, $data, $relationship);
        $this->assertRequestExists($relationship, $data, $actor, $relationship);
        $this->assertTrustedProvenance($relationship, $data, $actor);
        $matches = $this->assertIdentityMatch($school, $parentUser, $guardian, $data, $actor);

        if ($relationship->verification_status === RelationshipVerificationStatus::Rejected) {
            $this->deny(
                GuardianAccessDenialReason::RelationshipStateConflict,
                $data,
                $actor,
                $relationship,
            );
        }

        if ($relationship->verification_status === RelationshipVerificationStatus::Verified
            && $relationship->portal_access_status === RelationshipPortalAccessStatus::Authorized
            && $relationship->isPortalAuthorized()) {
            return $relationship;
        }

        $this->assertDatesValid($relationship, $data, $actor);

        $relationship->update($this->authorizationAttributes($matches));
        $relationship->refresh();

        $this->recordHistory(
            $relationship,
            'portal_access_verified',
            'Portal access approved by authorized school actor',
            $actor,
            [
                'verification_status' => RelationshipVerificationStatus::Verified->value,
                'portal_access_status' => RelationshipPortalAccessStatus::Authorized->value,
                'provenance_code' => 'LINKED_HISTORY_WITH_AUTHORIZED_ACTOR',
                'evidence_codes' => $this->evidenceCodes($matches),
            ],
        );

        $this->auditRelationshipAction(
            AuditAction::GuardianRelationshipVerified,
            $actor,
            [
                'reason_code' => 'AUTHORIZED_REVIEW_APPROVED',
                'provenance_code' => 'LINKED_HISTORY_WITH_AUTHORIZED_ACTOR',
                'evidence_codes' => $this->evidenceCodes($matches),
            ],
            $relationship,
        );

        return $relationship;
    }

    /**
     * Persist an explicit rejection instead of only throwing an exception.
     */
    public function reject(
        GuardianRelationshipVerificationData $data,
        ?User $actor = null,
        GuardianAccessDenialReason $reason = GuardianAccessDenialReason::RelationshipUnverified,
    ): StudentGuardianRelationship {
        $school = $this->resolveSchool();
        $actor = $this->resolveActor($actor, $school, $data);
        $relationship = $this->resolveRelationship($data->relationshipId, $school, $data, $actor);

        $this->authorizeActor($actor, $relationship, 'verify', $data);
        $this->assertNoRelationshipConflict($relationship, $data, $actor);
        [$guardian] = $this->assertSubjectOwnership($relationship, $school, $data, $actor);
        $this->assertTargetSeparation($actor, $guardian, $data, $relationship);

        if ($relationship->verification_status === RelationshipVerificationStatus::Verified
            && $relationship->portal_access_status === RelationshipPortalAccessStatus::Authorized) {
            $this->deny(
                GuardianAccessDenialReason::RelationshipStateConflict,
                $data,
                $actor,
                $relationship,
            );
        }

        $relationship->update([
            'verification_status' => RelationshipVerificationStatus::Rejected,
            'portal_access_status' => RelationshipPortalAccessStatus::NotAuthorized,
            'verified_at' => null,
            'portal_authorized_at' => null,
            'portal_revoked_at' => now(),
            'verification_evidence_code' => null,
            'authorization_reason_code' => $reason->value,
        ]);
        $relationship->refresh();

        $this->recordHistory(
            $relationship,
            'portal_access_rejected',
            'Portal access review rejected by authorized school actor',
            $actor,
            ['reason_code' => $reason->value],
        );

        $this->auditRelationshipAction(
            AuditAction::GuardianRelationshipRejected,
            $actor,
            ['reason_code' => $reason->value],
            $relationship,
        );

        return $relationship;
    }

    /**
     * Revoke portal access immediately while preserving the relationship row.
     */
    public function revoke(
        GuardianRelationshipRevocationData $data,
        ?User $actor = null,
    ): StudentGuardianRelationship {
        $school = $this->resolveSchool();
        $actor = $this->resolveActor($actor, $school, $data);
        $relationship = $this->resolveRelationship($data->relationshipId, $school, $data, $actor);

        $this->authorizeActor($actor, $relationship, 'revoke', $data);
        [$guardian] = $this->assertSubjectOwnership($relationship, $school, $data, $actor);
        $this->assertTargetSeparation($actor, $guardian, $data, $relationship);

        $relationship->update([
            'portal_access_status' => RelationshipPortalAccessStatus::Revoked,
            'portal_revoked_at' => now(),
            'authorization_reason_code' => $data->reason->value,
        ]);
        $relationship->refresh();

        $this->recordHistory(
            $relationship,
            'portal_access_revoked',
            'Portal access revoked by authorized school actor',
            $actor,
            [
                'portal_access_status' => RelationshipPortalAccessStatus::Revoked->value,
                'reason_code' => $data->reason->value,
            ],
        );

        $this->auditRelationshipAction(
            AuditAction::GuardianRelationshipRevoked,
            $actor,
            ['reason_code' => $data->reason->value],
            $relationship,
        );

        return $relationship;
    }

    private function resolveSchool(): School
    {
        $tenant = tenant();

        if (! $tenant instanceof School) {
            $this->deny(GuardianAccessDenialReason::TenantContextMissing);
        }

        if ($tenant->trashed() || ! $tenant->isActive()) {
            $this->deny(GuardianAccessDenialReason::SchoolInactive);
        }

        return $tenant;
    }

    /**
     * @param  GuardianRelationshipVerificationData|GuardianRelationshipRevocationData  $data
     */
    private function resolveActor(
        ?User $actor,
        School $school,
        GuardianRelationshipVerificationData|GuardianRelationshipRevocationData $data,
    ): User {
        $authenticated = Auth::user();

        if (! $authenticated instanceof User) {
            $this->deny(GuardianAccessDenialReason::ActorNotAuthenticated, $data, $actor);
        }

        if ($actor !== null && (int) $actor->getKey() !== (int) $authenticated->getKey()) {
            $this->deny(GuardianAccessDenialReason::ActorMismatch, $data, $actor);
        }

        $user = $authenticated;

        if ($user->trashed() || ! $user->isActive()) {
            $this->deny(GuardianAccessDenialReason::ActorNotActive, $data, $user);
        }

        if ((string) $user->school_id !== (string) $school->getKey()) {
            $this->deny(GuardianAccessDenialReason::ActorSchoolMismatch, $data, $user);
        }

        if ($user->hasRole('parent')) {
            $this->deny(GuardianAccessDenialReason::ActorNotAuthorized, $data, $user);
        }

        return $user;
    }

    /**
     * @param  GuardianRelationshipVerificationData|GuardianRelationshipRevocationData  $data
     */
    private function authorizeActor(
        User $actor,
        StudentGuardianRelationship $relationship,
        string $ability,
        GuardianRelationshipVerificationData|GuardianRelationshipRevocationData $data,
    ): void {
        if (! Gate::forUser($actor)->allows($ability, $relationship)) {
            $this->deny(GuardianAccessDenialReason::ActorNotAuthorized, $data, $actor, $relationship);
        }
    }

    /**
     * @param  GuardianRelationshipVerificationData|GuardianRelationshipRevocationData  $data
     */
    private function resolveRelationship(
        int $relationshipId,
        School $school,
        GuardianRelationshipVerificationData|GuardianRelationshipRevocationData $data,
        ?User $actor,
    ): StudentGuardianRelationship {
        $relationship = StudentGuardianRelationship::query()
            ->withTrashed()
            ->forSchool($school->getKey())
            ->whereNotNull('guardian_id')
            ->whereKey($relationshipId)
            ->first();

        if (! $relationship instanceof StudentGuardianRelationship) {
            $this->deny(GuardianAccessDenialReason::RelationshipNotFound, $data, $actor);
        }

        if ($relationship->trashed()) {
            $this->deny(GuardianAccessDenialReason::RelationshipRevoked, $data, $actor, $relationship);
        }

        if ((string) $relationship->school_id !== (string) $school->getKey()) {
            $this->deny(GuardianAccessDenialReason::RelationshipSchoolMismatch, $data, $actor, $relationship);
        }

        return $relationship;
    }

    /**
     * Resolve the target records from the durable relationship. These checks
     * establish ownership and identity; they are never relationship proof.
     *
     * @param  GuardianRelationshipVerificationData|GuardianRelationshipRevocationData  $data
     * @return array{0: Guardian, 1: Student, 2: User}
     */
    private function assertSubjectOwnership(
        StudentGuardianRelationship $relationship,
        School $school,
        GuardianRelationshipVerificationData|GuardianRelationshipRevocationData $data,
        ?User $actor,
    ): array {
        $guardian = Guardian::query()
            ->forSchool($school->getKey())
            ->whereKey($relationship->guardian_id)
            ->first();

        if (! $guardian instanceof Guardian) {
            $this->deny(GuardianAccessDenialReason::GuardianAccountMissing, $data, $actor, $relationship);
        }

        $student = Student::query()
            ->forSchool($school->getKey())
            ->whereKey($relationship->student_id)
            ->first();

        if (! $student instanceof Student) {
            $this->deny(GuardianAccessDenialReason::StudentNotFound, $data, $actor, $relationship);
        }

        $parentUser = User::query()->whereKey($guardian->user_id)->first();

        if (! $parentUser instanceof User || $parentUser->trashed()) {
            $this->deny(GuardianAccessDenialReason::UserNotFound, $data, $actor, $relationship);
        }

        if (! $parentUser->isActive()) {
            $this->deny(GuardianAccessDenialReason::UserNotActive, $data, $actor, $relationship);
        }

        if ((string) $parentUser->school_id !== (string) $school->getKey()) {
            $this->deny(GuardianAccessDenialReason::TenantContextMismatch, $data, $actor, $relationship);
        }

        if (! $parentUser->hasRole('parent')) {
            $this->deny(GuardianAccessDenialReason::ParentRoleMissing, $data, $actor, $relationship);
        }

        if ($guardian->trashed()
            || (string) $guardian->school_id !== (string) $school->getKey()
            || ! $guardian->isActive()) {
            $this->deny(GuardianAccessDenialReason::GuardianInactive, $data, $actor, $relationship);
        }

        if ((string) $relationship->school_id !== (string) $school->getKey()) {
            $this->deny(GuardianAccessDenialReason::RelationshipSchoolMismatch, $data, $actor, $relationship);
        }

        if ((int) $relationship->guardian_id !== (int) $guardian->getKey()) {
            $this->deny(GuardianAccessDenialReason::RelationshipGuardianMismatch, $data, $actor, $relationship);
        }

        if ((int) $relationship->student_id !== (int) $student->getKey()) {
            $this->deny(GuardianAccessDenialReason::RelationshipStudentMismatch, $data, $actor, $relationship);
        }

        return [$guardian, $student, $parentUser];
    }

    /**
     * @param  GuardianRelationshipVerificationData|GuardianRelationshipRevocationData  $data
     */
    private function assertTargetSeparation(
        User $actor,
        Guardian $guardian,
        GuardianRelationshipVerificationData|GuardianRelationshipRevocationData $data,
        StudentGuardianRelationship $relationship,
    ): void {
        if ($guardian->user_id !== null && (int) $guardian->user_id === (int) $actor->getKey()) {
            $this->deny(GuardianAccessDenialReason::ActorTargetMismatch, $data, $actor, $relationship);
        }
    }

    /**
     * @param  GuardianRelationshipVerificationData|GuardianRelationshipRevocationData  $data
     */
    private function assertNoRelationshipConflict(
        StudentGuardianRelationship $relationship,
        GuardianRelationshipVerificationData|GuardianRelationshipRevocationData $data,
        ?User $actor,
    ): void {
        $count = StudentGuardianRelationship::query()
            ->forSchool((string) $relationship->school_id)
            ->forGuardian((int) $relationship->guardian_id)
            ->forStudent((int) $relationship->student_id)
            ->whereNotNull('guardian_id')
            ->count();

        if ($count > 1) {
            $this->deny(GuardianAccessDenialReason::RelationshipConflict, $data, $actor, $relationship);
        }
    }

    /**
     * Task 11.1 is used only for identity resolution here. It never proves the
     * Guardian–Student relationship, which comes from trusted link provenance.
     *
     * @return list<IdentityMatchResult>
     */
    private function assertIdentityMatch(
        School $school,
        User $parentUser,
        Guardian $guardian,
        GuardianRelationshipVerificationData $data,
        ?User $actor,
    ): array {
        $matches = [
            $this->matcher->match(new IdentityMatchRequest(
                entityType: MatchableEntityType::User,
                schoolId: (string) $school->getKey(),
                userId: $parentUser->getKey(),
                guardianId: $guardian->getKey(),
                expectedRole: 'parent',
            )),
            $this->matcher->match(new IdentityMatchRequest(
                entityType: MatchableEntityType::Guardian,
                schoolId: (string) $school->getKey(),
                guardianNumber: $guardian->guardian_number,
                guardianId: $guardian->getKey(),
                userId: $parentUser->getKey(),
            )),
        ];

        foreach ($matches as $index => $result) {
            if ($result->outcome !== IdentityMatchOutcome::ExactMatch
                || ! $result->automaticReusePermitted
                || ! $result->candidateFound) {
                $this->deny(GuardianAccessDenialReason::IdentityMatchRejected, $data, $actor);
            }

            $expectedId = $index === 0 ? $parentUser->getKey() : $guardian->getKey();

            if ((string) $result->candidateId !== (string) $expectedId) {
                $this->deny(GuardianAccessDenialReason::IdentityMatchCandidateMismatch, $data, $actor);
            }
        }

        return $matches;
    }

    /**
     * @param  GuardianRelationshipVerificationData|GuardianRelationshipRevocationData  $data
     */
    private function assertRequestExists(
        StudentGuardianRelationship $relationship,
        GuardianRelationshipVerificationData|GuardianRelationshipRevocationData $data,
        ?User $actor,
        ?StudentGuardianRelationship $auditable = null,
    ): void {
        if (! $this->requestExists($relationship)) {
            $this->deny(
                GuardianAccessDenialReason::RelationshipRequestMissing,
                $data,
                $actor,
                $auditable,
            );
        }
    }

    private function requestExists(StudentGuardianRelationship $relationship): bool
    {
        return StudentGuardianRelationshipHistory::query()
            ->where('school_id', $relationship->school_id)
            ->where('student_guardian_id', $relationship->getKey())
            ->where('student_id', $relationship->student_id)
            ->where('guardian_id', $relationship->guardian_id)
            ->where('event', 'portal_access_requested')
            ->whereNotNull('user_id')
            ->exists();
    }

    /**
     * @param  GuardianRelationshipVerificationData|GuardianRelationshipRevocationData  $data
     */
    private function assertTrustedProvenance(
        StudentGuardianRelationship $relationship,
        GuardianRelationshipVerificationData|GuardianRelationshipRevocationData $data,
        ?User $actor,
    ): void {
        $link = StudentGuardianRelationshipHistory::query()
            ->where('school_id', $relationship->school_id)
            ->where('student_guardian_id', $relationship->getKey())
            ->where('student_id', $relationship->student_id)
            ->where('guardian_id', $relationship->guardian_id)
            ->where('event', 'linked')
            ->whereNotNull('user_id')
            ->first();

        if (! $link instanceof StudentGuardianRelationshipHistory
            || ! GuardianLinkProvenance::isTrustedHistory($link)) {
            $this->deny(
                GuardianAccessDenialReason::RelationshipProvenanceMissing,
                $data,
                $actor,
                $relationship,
            );
        }

    }

    /**
     * @param  GuardianRelationshipVerificationData|GuardianRelationshipRevocationData  $data
     */
    private function assertDatesValid(
        StudentGuardianRelationship $relationship,
        GuardianRelationshipVerificationData|GuardianRelationshipRevocationData $data,
        ?User $actor,
    ): void {
        $today = Carbon::today();

        if ($relationship->relationship_start_date === null || $today->lt($relationship->relationship_start_date)) {
            $this->deny(
                GuardianAccessDenialReason::RelationshipNotStarted,
                $data,
                $actor,
                $relationship,
            );
        }

        if ($relationship->relationship_end_date !== null && $today->gt($relationship->relationship_end_date)) {
            $this->deny(
                GuardianAccessDenialReason::RelationshipExpired,
                $data,
                $actor,
                $relationship,
            );
        }
    }

    /**
     * @param  list<IdentityMatchResult>  $matches
     * @return array<string, mixed>
     */
    private function authorizationAttributes(array $matches): array
    {
        $now = now();

        return [
            'verification_status' => RelationshipVerificationStatus::Verified,
            'portal_access_status' => RelationshipPortalAccessStatus::Authorized,
            'verified_at' => $now,
            'portal_authorized_at' => $now,
            'portal_revoked_at' => null,
            'verification_evidence_code' => 'LINKED_HISTORY|IDENTITY_MATCH:'.implode('|', $this->evidenceCodes($matches)),
            'authorization_reason_code' => 'AUTHORIZED_REVIEW_APPROVED',
        ];
    }

    /**
     * @param  list<IdentityMatchResult>  $matches
     * @return list<string>
     */
    private function evidenceCodes(array $matches): array
    {
        $codes = [];

        foreach ($matches as $result) {
            foreach ($result->evidence as $evidence) {
                $codes[] = $evidence->value;
            }
        }

        return array_values(array_unique($codes));
    }

    private function recordHistory(
        StudentGuardianRelationship $relationship,
        string $event,
        string $description,
        User $actor,
        array $payload,
    ): void {
        StudentGuardianRelationshipHistory::create([
            'school_id' => $relationship->school_id,
            'student_guardian_id' => $relationship->getKey(),
            'student_id' => $relationship->student_id,
            'guardian_id' => $relationship->guardian_id,
            'event' => $event,
            'description' => $description,
            'payload' => $payload,
            'user_id' => (string) $actor->getKey(),
            'user_type' => $actor::class,
            'occurred_at' => now(),
        ]);
    }

    private function auditRelationshipAction(
        AuditAction $action,
        User $actor,
        array $values,
        ?StudentGuardianRelationship $relationship = null,
    ): void {
        $targetIds = $relationship !== null
            ? [
                'guardian_id' => (string) $relationship->guardian_id,
                'student_id' => (string) $relationship->student_id,
            ]
            : [];

        $schoolId = $relationship !== null ? $relationship->school_id : tenant()?->getKey();

        $this->audit->record([
            'school_id' => $schoolId,
            'auditable_type' => StudentGuardianRelationship::class,
            'auditable_id' => (string) ($relationship?->getKey() ?? 'current'),
            'action' => $action->value,
            'event' => class_basename(StudentGuardianRelationship::class),
            'description' => match ($action) {
                AuditAction::GuardianRelationshipRequested => 'Lien tuteur–élève demandé par un acteur autorisé',
                AuditAction::GuardianRelationshipVerified => 'Lien tuteur–élève approuvé par un acteur autorisé',
                AuditAction::GuardianRelationshipRejected => 'Lien tuteur–élève refusé ou tentative refusée',
                AuditAction::GuardianRelationshipRevoked => 'Accès au portail révoqué par un acteur autorisé',
                default => 'Action sur le lien tuteur–élève',
            },
            'new_values' => [
                'actor_id' => (string) $actor->getKey(),
                ...$targetIds,
                ...$values,
            ],
            'user_id' => (string) $actor->getKey(),
            'user_type' => $actor::class,
        ]);
    }

    /**
     * @param  GuardianRelationshipVerificationData|GuardianRelationshipRevocationData  $data
     */
    private function deny(
        GuardianAccessDenialReason $reason,
        GuardianRelationshipVerificationData|GuardianRelationshipRevocationData|null $data = null,
        ?User $actor = null,
        ?StudentGuardianRelationship $relationship = null,
    ): never {
        $auditable = $this->audit;

        $schoolId = $relationship !== null ? $relationship->school_id : tenant()?->getKey();

        $auditable->record([
            'school_id' => $schoolId,
            'auditable_type' => StudentGuardianRelationship::class,
            'auditable_id' => (string) ($relationship?->getKey() ?? 'current'),
            'action' => AuditAction::GuardianRelationshipRejected->value,
            'event' => 'guardian_relationship_denied',
            'description' => 'Tentative de relation tuteur–élève refusée',
            'new_values' => [
                'reason_code' => $reason->value,
                'actor_id' => $actor !== null ? (string) $actor->getKey() : null,
                'guardian_id' => $relationship !== null ? (string) $relationship->guardian_id : null,
                'student_id' => $relationship !== null ? (string) $relationship->student_id : null,
            ],
            'user_id' => $actor !== null ? (string) $actor->getKey() : null,
            'user_type' => $actor !== null ? $actor::class : null,
        ]);

        if ($relationship instanceof StudentGuardianRelationship && $actor instanceof User && ! $relationship->trashed()) {
            StudentGuardianRelationshipHistory::create([
                'school_id' => $relationship->school_id,
                'student_guardian_id' => $relationship->getKey(),
                'student_id' => $relationship->student_id,
                'guardian_id' => $relationship->guardian_id,
                'event' => 'portal_access_rejected',
                'description' => 'Tentative refusée par les règles de sécurité',
                'payload' => ['reason_code' => $reason->value],
                'user_id' => (string) $actor->getKey(),
                'user_type' => $actor::class,
                'occurred_at' => now(),
            ]);
        }

        throw GuardianRelationshipVerificationException::because($reason);
    }
}
