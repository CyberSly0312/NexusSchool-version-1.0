<?php

declare(strict_types=1);

namespace App\Domains\Guardian\Application\Services;

use App\Domains\Guardian\Domain\Enums\GuardianAccessDenialReason;
use App\Domains\Guardian\Domain\Enums\GuardianChildOperation;
use App\Domains\Guardian\Domain\Enums\RelationshipPortalAccessStatus;
use App\Domains\Guardian\Domain\Enums\RelationshipVerificationStatus;
use App\Domains\Guardian\Domain\Exceptions\GuardianChildAccessDeniedException;
use App\Domains\Guardian\Infrastructure\Models\Guardian;
use App\Domains\Guardian\Infrastructure\Models\StudentGuardianRelationship;
use App\Domains\School\Models\School;
use App\Domains\Student\Infrastructure\Models\Student;
use App\Domains\System\Enums\AuditAction;
use App\Domains\System\Services\AuditService;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * Centralized child-level authorization for every Parent data-access surface.
 *
 * The authenticated user is always resolved internally from the session. No
 * public method accepts a caller-controlled user ID, so a request cannot obtain
 * authorization decisions for another Parent account.
 */
final class GuardianChildAccessService
{
    public function __construct(
        private readonly AuditService $audit = new AuditService(),
    ) {
    }

    /**
     * Assert that the current authenticated request has a valid Parent/Guardian
     * identity for the initialized tenant. Used by list surfaces with no record.
     */
    public function assertParentContext(): Guardian
    {
        $school = $this->resolveSchool();
        $user = $this->resolveUser($this->authenticatedUser(), $school);

        return $this->resolveGuardian($user, $school);
    }

    /**
     * Assert authorization for one specific child and operation.
     */
    public function assertCanAccess(
        int|string $studentId,
        GuardianChildOperation $operation = GuardianChildOperation::View,
    ): StudentGuardianRelationship {
        if (! $operation->isPermitted()) {
            $this->deny(
                GuardianAccessDenialReason::OperationNotPermitted,
                Student::class,
                $studentId,
                $operation,
            );
        }

        $school = $this->resolveSchool();
        $user = $this->resolveUser($this->authenticatedUser(), $school);
        $guardian = $this->resolveGuardian($user, $school);
        $student = $this->resolveStudent($studentId, $school);

        $relationship = StudentGuardianRelationship::query()
            ->withTrashed()
            ->forSchool($school->getKey())
            ->forGuardian($guardian->getKey())
            ->forStudent($student->getKey())
            ->first();

        if (! $relationship instanceof StudentGuardianRelationship) {
            $this->deny(
                GuardianAccessDenialReason::RelationshipNotFound,
                Student::class,
                $student->getKey(),
                $operation,
            );
        }

        if ($relationship->trashed()) {
            $this->deny(
                GuardianAccessDenialReason::RelationshipRevoked,
                Student::class,
                $student->getKey(),
                $operation,
            );
        }

        if ((string) $relationship->school_id !== (string) $school->getKey()) {
            $this->deny(
                GuardianAccessDenialReason::RelationshipSchoolMismatch,
                Student::class,
                $student->getKey(),
                $operation,
            );
        }

        if ((int) $relationship->guardian_id !== (int) $guardian->getKey()) {
            $this->deny(
                GuardianAccessDenialReason::RelationshipGuardianMismatch,
                Student::class,
                $student->getKey(),
                $operation,
            );
        }

        if ((int) $relationship->student_id !== (int) $student->getKey()) {
            $this->deny(
                GuardianAccessDenialReason::RelationshipStudentMismatch,
                Student::class,
                $student->getKey(),
                $operation,
            );
        }

        if ($relationship->verification_status !== RelationshipVerificationStatus::Verified) {
            $this->deny(
                GuardianAccessDenialReason::RelationshipUnverified,
                Student::class,
                $student->getKey(),
                $operation,
            );
        }

        if ($relationship->portal_access_status === RelationshipPortalAccessStatus::Revoked) {
            $this->deny(
                GuardianAccessDenialReason::RelationshipRevoked,
                Student::class,
                $student->getKey(),
                $operation,
            );
        }

        if ($relationship->portal_access_status !== RelationshipPortalAccessStatus::Authorized) {
            $this->deny(
                GuardianAccessDenialReason::RelationshipNotAuthorized,
                Student::class,
                $student->getKey(),
                $operation,
            );
        }

        if ($relationship->relationship_start_date === null
            || Carbon::today()->lt($relationship->relationship_start_date)) {
            $this->deny(
                GuardianAccessDenialReason::RelationshipNotStarted,
                Student::class,
                $student->getKey(),
                $operation,
            );
        }

        if ($relationship->relationship_end_date !== null
            && Carbon::today()->gt($relationship->relationship_end_date)) {
            $this->deny(
                GuardianAccessDenialReason::RelationshipExpired,
                Student::class,
                $student->getKey(),
                $operation,
            );
        }

        if (! $relationship->hasTrustedLinkProvenance()) {
            $this->deny(
                GuardianAccessDenialReason::RelationshipProvenanceMissing,
                Student::class,
                $student->getKey(),
                $operation,
            );
        }

        if (! $relationship->isPortalAuthorized()) {
            $this->deny(
                GuardianAccessDenialReason::RelationshipNotAuthorized,
                Student::class,
                $student->getKey(),
                $operation,
            );
        }

        return $relationship;
    }

    /**
     * Apply the same authorization rules to a Student query. Callers must use
     * this for tables, exports and REST/query endpoints.
     */
    public function scopeStudentQuery(
        Builder $query,
        GuardianChildOperation $operation = GuardianChildOperation::View,
    ): Builder {
        if (! $operation->isPermitted()) {
            return $query->whereRaw('1 = 0');
        }

        try {
            $school = $this->resolveSchool();
            $user = $this->resolveUser($this->authenticatedUser(), $school);
            $guardian = $this->resolveGuardian($user, $school);
        } catch (GuardianChildAccessDeniedException) {
            return $query->whereRaw('1 = 0');
        }

        $studentIds = StudentGuardianRelationship::query()
            ->forSchool($school->getKey())
            ->forGuardian($guardian->getKey())
            ->portalAuthorized()
            ->get()
            ->filter(fn (StudentGuardianRelationship $relationship): bool => $relationship->hasTrustedLinkProvenance())
            ->pluck('student_id')
            ->unique()
            ->all();

        if ($studentIds === []) {
            return $query->whereRaw('1 = 0');
        }

        return $query
            ->where('school_id', $school->getKey())
            ->whereIn($query->getModel()->qualifyColumn('id'), $studentIds);
    }

    /**
     * Record a successful child record view with IDs only.
     */
    public function recordChildViewed(StudentGuardianRelationship $relationship): void
    {
        $user = $this->authenticatedUser();

        $this->audit->record([
            'school_id' => $relationship->school_id,
            'auditable_type' => StudentGuardianRelationship::class,
            'auditable_id' => (string) $relationship->getKey(),
            'action' => AuditAction::GuardianChildViewed->value,
            'event' => class_basename($relationship),
            'description' => 'Fiche élève consultée par un tuteur autorisé',
            'new_values' => [
                'actor_id' => (string) $user->getKey(),
                'student_id' => (string) $relationship->student_id,
                'guardian_id' => (string) $relationship->guardian_id,
            ],
            'user_id' => (string) $user->getKey(),
            'user_type' => $user::class,
        ]);
    }

    private function resolveSchool(): School
    {
        $tenant = tenant();

        if (! $tenant instanceof School) {
            $this->deny(
                GuardianAccessDenialReason::TenantContextMissing,
                School::class,
                'current',
            );
        }

        if ($tenant->trashed() || ! $tenant->isActive()) {
            $this->deny(
                GuardianAccessDenialReason::SchoolInactive,
                School::class,
                $tenant->getKey(),
            );
        }

        return $tenant;
    }

    private function authenticatedUser(): User
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            $this->deny(
                GuardianAccessDenialReason::ActorNotAuthenticated,
                User::class,
                'current',
            );
        }

        return $user;
    }

    private function resolveUser(User $user, School $school): User
    {
        if ($user->trashed() || ! $user->isActive()) {
            $this->deny(
                GuardianAccessDenialReason::UserNotActive,
                User::class,
                $user->getKey(),
            );
        }

        if ((string) $user->school_id !== (string) $school->getKey()) {
            $this->deny(
                GuardianAccessDenialReason::TenantContextMismatch,
                User::class,
                $user->getKey(),
            );
        }

        if (! $user->hasRole('parent')) {
            $this->deny(
                GuardianAccessDenialReason::ParentRoleMissing,
                User::class,
                $user->getKey(),
            );
        }

        return $user;
    }

    private function resolveGuardian(User $user, School $school): Guardian
    {
        $guardians = Guardian::query()
            ->forSchool($school->getKey())
            ->where('user_id', $user->getKey())
            ->get();

        if ($guardians->count() === 0) {
            $this->deny(
                GuardianAccessDenialReason::GuardianAccountMissing,
                Guardian::class,
                $user->getKey(),
            );
        }

        if ($guardians->count() > 1) {
            $this->deny(
                GuardianAccessDenialReason::GuardianAccountAmbiguous,
                Guardian::class,
                $user->getKey(),
            );
        }

        $guardian = $guardians->first();

        if (! $guardian instanceof Guardian) {
            $this->deny(
                GuardianAccessDenialReason::GuardianAccountMissing,
                Guardian::class,
                $user->getKey(),
            );
        }

        if ($guardian->trashed()
            || (string) $guardian->school_id !== (string) $school->getKey()
            || ! $guardian->isActive()) {
            $this->deny(
                GuardianAccessDenialReason::GuardianInactive,
                Guardian::class,
                $guardian->getKey(),
            );
        }

        return $guardian;
    }

    private function resolveStudent(int|string $studentId, School $school): Student
    {
        $student = Student::query()
            ->forSchool($school->getKey())
            ->find($studentId);

        if (! $student instanceof Student || $student->trashed()) {
            $this->deny(
                GuardianAccessDenialReason::StudentNotFound,
                Student::class,
                (string) $studentId,
            );
        }

        if ((string) $student->school_id !== (string) $school->getKey()) {
            $this->deny(
                GuardianAccessDenialReason::StudentSchoolMismatch,
                Student::class,
                $student->getKey(),
            );
        }

        return $student;
    }

    private function deny(
        GuardianAccessDenialReason $reason,
        string $auditableType,
        int|string $auditableId,
        ?GuardianChildOperation $operation = null,
    ): never {
        $user = Auth::user();

        $this->audit->record([
            'school_id' => tenant()?->getKey(),
            'auditable_type' => $auditableType,
            'auditable_id' => (string) $auditableId,
            'action' => AuditAction::GuardianChildAccessDenied->value,
            'event' => $operation?->value,
            'description' => 'Accès à un élève refusé par l’autorisation centralisée',
            'new_values' => [
                'reason_code' => $reason->value,
                'operation' => $operation?->value,
                'actor_id' => $user instanceof User ? (string) $user->getKey() : null,
            ],
            'user_id' => $user instanceof User ? (string) $user->getKey() : null,
            'user_type' => $user instanceof User ? $user::class : null,
        ]);

        throw GuardianChildAccessDeniedException::because($reason);
    }
}
