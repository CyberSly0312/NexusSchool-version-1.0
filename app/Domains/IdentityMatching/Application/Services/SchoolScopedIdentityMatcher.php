<?php

declare(strict_types=1);

namespace App\Domains\IdentityMatching\Application\Services;

use App\Domains\Guardian\Infrastructure\Models\Guardian;
use App\Domains\Guardian\Infrastructure\Models\StudentGuardianRelationship;
use App\Domains\IdentityMatching\Application\DTOs\IdentityMatchRequest;
use App\Domains\IdentityMatching\Application\DTOs\IdentityMatchResult;
use App\Domains\IdentityMatching\Domain\Enums\IdentityEvidenceType;
use App\Domains\IdentityMatching\Domain\Enums\IdentityMatchOutcome;
use App\Domains\IdentityMatching\Domain\Enums\IdentityMatchReasonCode;
use App\Domains\IdentityMatching\Domain\Enums\MatchableEntityType;
use App\Domains\School\Models\School;
use App\Domains\Student\Infrastructure\Models\Student;
use App\Domains\Student\Infrastructure\Models\StudentGuardian;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Read-only, school-scoped identity resolution foundation.
 *
 * This service never creates, links, merges, activates or mutates a record. It
 * returns a deterministic structured result so a later approved orchestrator
 * can decide what to do. Every candidate query is rooted in the tenant-resolved
 * school; no candidate is found globally and filtered afterwards.
 */
final class SchoolScopedIdentityMatcher
{
    public function __construct(
        private readonly IdentityNormalizer $normalizer = new IdentityNormalizer(),
    ) {
    }

    public function match(IdentityMatchRequest $request): IdentityMatchResult
    {
        $context = $this->resolveTrustedSchool($request);
        if ($context['reason'] !== null || $context['school'] === null) {
            return $this->conflict($request, $context['reason'] ?? IdentityMatchReasonCode::TenantContextMissing);
        }

        $schoolId = (string) $context['school']->getKey();

        $email = $this->normalizer->normalizeEmail($request->email);
        $phone = $this->normalizer->normalizePhone($request->phone);
        $firstName = $this->normalizer->normalizeName($request->firstName);
        $lastName = $this->normalizer->normalizeName($request->lastName);
        $studentNumber = $this->normalizer->normalizeIdentifier($request->studentNumber);
        $admissionNumber = $this->normalizer->normalizeIdentifier($request->admissionNumber);
        $guardianNumber = $this->normalizer->normalizeIdentifier($request->guardianNumber);
        $dateOfBirth = $this->normalizer->normalizeDate($request->dateOfBirth);

        if ($request->embeddedGuardianReference && $this->embeddedReferenceExists(
            $schoolId,
            $email,
            $phone,
            $firstName,
            $lastName,
        )) {
            return new IdentityMatchResult(
                IdentityMatchOutcome::PossibleMatch,
                $request->entityType,
                null,
                [IdentityEvidenceType::EmbeddedGuardianReference],
                [IdentityMatchReasonCode::EmbeddedReferenceOnly],
                false,
                false,
            );
        }

        $candidates = $this->candidateQuery(
            $request,
            $schoolId,
            $email,
            $phone,
            $firstName,
            $lastName,
            $studentNumber,
            $admissionNumber,
            $guardianNumber,
        )->limit(2)->get()->all();

        if (count($candidates) > 1) {
            return $this->conflict($request, IdentityMatchReasonCode::MultipleCandidates);
        }

        if ($this->softDeletedConflictExists(
            $request,
            $schoolId,
            $email,
            $studentNumber,
            $admissionNumber,
            $guardianNumber,
        )) {
            return $this->conflict(
                $request,
                IdentityMatchReasonCode::SoftDeletedCandidate,
                [IdentityEvidenceType::SoftDeletedRecord],
            );
        }

        if ($email !== null && $this->crossSchoolEmailConflictExists($email, $schoolId)) {
            return $this->conflict(
                $request,
                IdentityMatchReasonCode::CrossSchoolEmailConflict,
                [IdentityEvidenceType::CrossSchoolConstraint],
            );
        }

        /** @var Student|Guardian|User|null $candidate */
        $candidate = $candidates[0] ?? null;

        if ($candidate === null) {
            // Embedded contact rows have no durable identity. Detect them even
            // when the caller does not explicitly ask, so they are never
            // mistaken for an absence of evidence or promoted to an account.
            if ($this->embeddedReferenceExists(
                $schoolId,
                $email,
                $phone,
                $firstName,
                $lastName,
            )) {
                return new IdentityMatchResult(
                    IdentityMatchOutcome::PossibleMatch,
                    $request->entityType,
                    null,
                    [IdentityEvidenceType::EmbeddedGuardianReference],
                    [IdentityMatchReasonCode::EmbeddedReferenceOnly],
                    false,
                    false,
                );
            }

            return new IdentityMatchResult(
                IdentityMatchOutcome::PossibleMatch,
                $request->entityType,
                null,
                [],
                [IdentityMatchReasonCode::NoCandidate],
                false,
                false,
            );
        }

        $candidateId = $candidate->getKey();
        $evidence = [];

        $durable = $this->hasDurableIdentifier(
            $request,
            $candidate,
            $schoolId,
            $studentNumber,
            $admissionNumber,
            $guardianNumber,
        );
        $emailMatches = $this->emailMatches($candidate, $email);
        $phoneMatches = $this->phoneMatches($candidate, $request->entityType, $phone);
        $nameMatches = $this->nameMatches($candidate, $firstName, $lastName);
        $dateOfBirthMatches = $this->dateOfBirthMatches($candidate, $dateOfBirth);
        $corroborated = $this->hasSameSchoolCorroboration($request, $candidate, $schoolId);

        if ($durable) {
            $evidence[] = IdentityEvidenceType::DurableSchoolIdentifier;
        }

        if ($emailMatches) {
            $evidence[] = IdentityEvidenceType::ExactNormalizedEmail;
        }

        if ($corroborated) {
            $evidence[] = IdentityEvidenceType::SameSchoolCorroboration;
        }

        if ($nameMatches) {
            $evidence[] = IdentityEvidenceType::NameSimilarity;
        }

        if ($dateOfBirthMatches) {
            $evidence[] = IdentityEvidenceType::DateOfBirthSupport;
        }

        if ($phoneMatches) {
            $evidence[] = IdentityEvidenceType::PhoneSimilarity;
        }

        if ($this->hasConflictingEvidence(
            $request,
            $candidate,
            $schoolId,
            $email,
            $studentNumber,
            $admissionNumber,
            $guardianNumber,
            $dateOfBirth,
        )) {
            $evidence[] = IdentityEvidenceType::ConflictingIdentity;

            return $this->conflict(
                $request,
                IdentityMatchReasonCode::ConflictingEvidence,
                $evidence,
            );
        }

        if ($request->entityType === MatchableEntityType::User) {
            /** @var User $candidate */
            if ($candidate->trashed() || ! $candidate->isActive()) {
                $evidence[] = IdentityEvidenceType::IneligibleAccount;

                return $this->conflict(
                    $request,
                    IdentityMatchReasonCode::IneligibleAccount,
                    $evidence,
                );
            }

            $expectedRole = $request->expectedRole !== null && trim($request->expectedRole) !== ''
                ? trim($request->expectedRole)
                : 'parent';

            if (! $candidate->hasRole($expectedRole)) {
                $evidence[] = IdentityEvidenceType::IneligibleAccount;

                return $this->conflict(
                    $request,
                    IdentityMatchReasonCode::RoleNotCompatible,
                    $evidence,
                );
            }
        }

        if ($emailMatches && $request->entityType === MatchableEntityType::Guardian) {
            return new IdentityMatchResult(
                IdentityMatchOutcome::ExactMatch,
                $request->entityType,
                $candidateId,
                $evidence,
                [IdentityMatchReasonCode::ExactEmailGuardianMatch],
                true,
                true,
            );
        }

        if ($emailMatches && $request->entityType === MatchableEntityType::User && $corroborated) {
            return new IdentityMatchResult(
                IdentityMatchOutcome::ExactMatch,
                $request->entityType,
                $candidateId,
                $evidence,
                [IdentityMatchReasonCode::ExactEmailWithCorroboration],
                true,
                true,
            );
        }

        if ($durable) {
            return new IdentityMatchResult(
                IdentityMatchOutcome::ExactMatch,
                $request->entityType,
                $candidateId,
                $evidence,
                [IdentityMatchReasonCode::DurableIdentifierMatch],
                true,
                true,
            );
        }

        if ($emailMatches) {
            return new IdentityMatchResult(
                IdentityMatchOutcome::PossibleMatch,
                $request->entityType,
                $candidateId,
                $evidence,
                [IdentityMatchReasonCode::ExactEmailWithoutCorroboration],
                false,
                true,
            );
        }

        if ($nameMatches && ! $phoneMatches) {
            return new IdentityMatchResult(
                IdentityMatchOutcome::PossibleMatch,
                $request->entityType,
                $candidateId,
                $evidence,
                [$dateOfBirthMatches
                    ? IdentityMatchReasonCode::NameAndDateOfBirthOnly
                    : IdentityMatchReasonCode::NameOnly],
                false,
                true,
            );
        }

        if ($phoneMatches) {
            return new IdentityMatchResult(
                IdentityMatchOutcome::PossibleMatch,
                $request->entityType,
                $candidateId,
                $evidence,
                [IdentityMatchReasonCode::PhoneOnly],
                false,
                true,
            );
        }

        return new IdentityMatchResult(
            IdentityMatchOutcome::PossibleMatch,
            $request->entityType,
            $candidateId,
            $evidence,
            [IdentityMatchReasonCode::NoCandidate],
            false,
            true,
        );
    }

    /**
     * @return array{school: School|null, reason: IdentityMatchReasonCode|null}
     */
    private function resolveTrustedSchool(IdentityMatchRequest $request): array
    {
        $tenant = tenant();

        if (! $tenant instanceof School) {
            return ['school' => null, 'reason' => IdentityMatchReasonCode::TenantContextMissing];
        }

        $tenantId = (string) $tenant->getKey();
        $requestedSchoolId = trim($request->schoolId);

        if ($tenantId === '' || $requestedSchoolId === '') {
            return ['school' => null, 'reason' => IdentityMatchReasonCode::TenantContextMissing];
        }

        if ($tenantId !== $requestedSchoolId) {
            return ['school' => null, 'reason' => IdentityMatchReasonCode::TenantContextMismatch];
        }

        if ($tenant->trashed() || ! $tenant->isActive()) {
            return ['school' => null, 'reason' => IdentityMatchReasonCode::TenantSchoolInactive];
        }

        return ['school' => $tenant, 'reason' => null];
    }

    private function embeddedReferenceExists(
        string $schoolId,
        ?string $email,
        ?string $phone,
        ?string $firstName,
        ?string $lastName,
    ): bool {
        if ($email === null && $phone === null && $firstName === null && $lastName === null) {
            return false;
        }

        return StudentGuardian::query()
            ->forSchool($schoolId)
            ->whereNull('guardian_id')
            ->where(function (Builder $query) use ($email, $phone, $firstName, $lastName): void {
                $first = true;

                if ($email !== null) {
                    $query->whereRaw('LOWER(TRIM(email)) = ?', [$email]);
                    $first = false;
                }

                if ($phone !== null) {
                    $first
                        ? $query->whereRaw($this->phoneExpression('phone').' = ?', [$phone])
                        : $query->orWhereRaw($this->phoneExpression('phone').' = ?', [$phone]);
                    $first = false;
                }

                if ($firstName !== null && $lastName !== null) {
                    $nameClause = function (Builder $query) use ($firstName, $lastName): void {
                        $query->whereRaw('LOWER(TRIM(first_name)) = ?', [$firstName])
                            ->whereRaw('LOWER(TRIM(last_name)) = ?', [$lastName]);
                    };

                    $first ? $query->where($nameClause) : $query->orWhere($nameClause);
                }
            })
            ->exists();
    }

    private function candidateQuery(
        IdentityMatchRequest $request,
        string $schoolId,
        ?string $email,
        ?string $phone,
        ?string $firstName,
        ?string $lastName,
        ?string $studentNumber,
        ?string $admissionNumber,
        ?string $guardianNumber,
    ): Builder {
        $query = match ($request->entityType) {
            MatchableEntityType::Student => Student::query()->forSchool($schoolId),
            MatchableEntityType::Guardian => Guardian::query()->forSchool($schoolId),
            MatchableEntityType::User => User::query()->where('school_id', $schoolId),
        };

        $phoneColumn = $request->entityType === MatchableEntityType::User ? 'phone' : 'phone_number';
        $guardianUserIds = [];

        if ($request->entityType === MatchableEntityType::User && $request->guardianId !== null) {
            $guardianUserIds = $this->userIdsForGuardian($schoolId, $request->guardianId);
        }

        $userIdColumn = $request->entityType === MatchableEntityType::User ? 'id' : 'user_id';

        $query->where(function (Builder $query) use (
            $request,
            $email,
            $phone,
            $phoneColumn,
            $firstName,
            $lastName,
            $studentNumber,
            $admissionNumber,
            $guardianNumber,
            $guardianUserIds,
            $userIdColumn,
        ): void {
            $first = true;

            if ($request->entityType === MatchableEntityType::Student && $studentNumber !== null) {
                $query->where('student_number', $studentNumber);
                $first = false;
            }

            if ($request->entityType === MatchableEntityType::Student && $admissionNumber !== null) {
                $first
                    ? $query->where('admission_number', $admissionNumber)
                    : $query->orWhere('admission_number', $admissionNumber);
                $first = false;
            }

            if ($request->entityType === MatchableEntityType::Guardian && $guardianNumber !== null) {
                $first
                    ? $query->where('guardian_number', $guardianNumber)
                    : $query->orWhere('guardian_number', $guardianNumber);
                $first = false;
            }

            if ($request->userId !== null) {
                $first
                    ? $query->where($userIdColumn, $request->userId)
                    : $query->orWhere($userIdColumn, $request->userId);
                $first = false;
            }

            if ($request->guardianId !== null && $request->entityType === MatchableEntityType::Guardian) {
                $first
                    ? $query->where('id', $request->guardianId)
                    : $query->orWhere('id', $request->guardianId);
                $first = false;
            }

            if ($guardianUserIds !== []) {
                $first
                    ? $query->whereIn('id', $guardianUserIds)
                    : $query->orWhereIn('id', $guardianUserIds);
                $first = false;
            }

            if ($email !== null) {
                $first
                    ? $query->whereRaw('LOWER(TRIM(email)) = ?', [$email])
                    : $query->orWhereRaw('LOWER(TRIM(email)) = ?', [$email]);
                $first = false;
            }

            if ($phone !== null) {
                $expression = $this->phoneExpression($phoneColumn).' = ?';
                $first
                    ? $query->whereRaw($expression, [$phone])
                    : $query->orWhereRaw($expression, [$phone]);
                $first = false;
            }

            if ($firstName !== null && $lastName !== null) {
                $nameClause = function (Builder $query) use ($firstName, $lastName): void {
                    $query->whereRaw('LOWER(TRIM(first_name)) = ?', [$firstName])
                        ->whereRaw('LOWER(TRIM(last_name)) = ?', [$lastName]);
                };

                $first ? $query->where($nameClause) : $query->orWhere($nameClause);
                $first = false;
            }

            if ($first) {
                $query->whereRaw('1 = 0');
            }
        });

        return $query;
    }

    /**
     * Resolve a durable same-school Guardian → User link only.
     *
     * A Guardian outside the current school is never consulted here, so this
     * cannot become a cross-tenant identity bridge.
     *
     * @return list<int|string>
     */
    private function userIdsForGuardian(string $schoolId, int|string $guardianId): array
    {
        /** @var Guardian|null $guardian */
        $guardian = Guardian::query()
            ->forSchool($schoolId)
            ->whereKey($guardianId)
            ->first();

        if (! $guardian instanceof Guardian || $guardian->user_id === null) {
            return [];
        }

        return [$guardian->user_id];
    }

    private function softDeletedConflictExists(
        IdentityMatchRequest $request,
        string $schoolId,
        ?string $email,
        ?string $studentNumber,
        ?string $admissionNumber,
        ?string $guardianNumber,
    ): bool {
        if ($request->entityType === MatchableEntityType::Student) {
            $query = Student::withTrashed()->forSchool($schoolId)->whereNotNull('deleted_at');

            if ($studentNumber !== null) {
                return (clone $query)->where('student_number', $studentNumber)->exists();
            }

            if ($admissionNumber !== null) {
                return (clone $query)->where('admission_number', $admissionNumber)->exists();
            }

            return $email !== null
                && (clone $query)->whereRaw('LOWER(TRIM(email)) = ?', [$email])->exists();
        }

        if ($request->entityType === MatchableEntityType::Guardian) {
            $query = Guardian::withTrashed()->forSchool($schoolId)->whereNotNull('deleted_at');

            if ($guardianNumber !== null) {
                return (clone $query)->where('guardian_number', $guardianNumber)->exists();
            }

            if ($request->guardianId !== null) {
                return (clone $query)->whereKey($request->guardianId)->exists();
            }

            return $email !== null
                && (clone $query)->whereRaw('LOWER(TRIM(email)) = ?', [$email])->exists();
        }

        $query = User::withTrashed()
            ->where('school_id', $schoolId)
            ->whereNotNull('deleted_at');

        if ($request->userId !== null) {
            return (clone $query)->whereKey($request->userId)->exists();
        }

        return $email !== null
            && (clone $query)->whereRaw('LOWER(TRIM(email)) = ?', [$email])->exists();
    }

    /**
     * Global email uniqueness constraint check, not a candidate lookup.
     *
     * No foreign-school identifier is ever selected, returned or exposed. The
     * only observable outcome is a generic conflict, as required by the
     * approved cross-school email policy.
     */
    private function crossSchoolEmailConflictExists(string $email, string $schoolId): bool
    {
        return User::withTrashed()
            ->whereRaw('LOWER(TRIM(email)) = ?', [$email])
            ->where('school_id', '!=', $schoolId)
            ->exists();
    }

    private function hasDurableIdentifier(
        IdentityMatchRequest $request,
        Model $candidate,
        string $schoolId,
        ?string $studentNumber,
        ?string $admissionNumber,
        ?string $guardianNumber,
    ): bool {
        if ($request->entityType === MatchableEntityType::Student) {
            return $this->matchesStoredIdentifier($candidate, 'student_number', $studentNumber)
                || $this->matchesStoredIdentifier($candidate, 'admission_number', $admissionNumber)
                || ($request->userId !== null
                    && (string) $candidate->getAttribute('user_id') === (string) $request->userId);
        }

        if ($request->entityType === MatchableEntityType::Guardian) {
            return $this->matchesStoredIdentifier($candidate, 'guardian_number', $guardianNumber)
                || ($request->guardianId !== null
                    && (string) $candidate->getKey() === (string) $request->guardianId)
                || ($request->userId !== null
                    && (string) $candidate->getAttribute('user_id') === (string) $request->userId);
        }

        if ($request->userId !== null && (string) $candidate->getKey() === (string) $request->userId) {
            return true;
        }

        if ($request->guardianId === null) {
            return false;
        }

        foreach ($this->userIdsForGuardian($schoolId, $request->guardianId) as $userId) {
            if ((string) $userId === (string) $candidate->getKey()) {
                return true;
            }
        }

        return false;
    }

    private function hasConflictingEvidence(
        IdentityMatchRequest $request,
        Model $candidate,
        string $schoolId,
        ?string $email,
        ?string $studentNumber,
        ?string $admissionNumber,
        ?string $guardianNumber,
        ?string $dateOfBirth,
    ): bool {
        if ($email !== null
            && $this->emailMatches($candidate, $email) === false
            && $candidate->getAttribute('email') !== null
        ) {
            return true;
        }

        if ($dateOfBirth !== null
            && $this->storedDateOfBirth($candidate) !== null
            && $this->dateOfBirthMatches($candidate, $dateOfBirth) === false
        ) {
            return true;
        }

        if ($request->entityType === MatchableEntityType::Student) {
            return $this->storedIdentifierConflicts($candidate, 'student_number', $studentNumber)
                || $this->storedIdentifierConflicts($candidate, 'admission_number', $admissionNumber)
                || ($request->userId !== null
                    && (string) $candidate->getAttribute('user_id') !== ''
                    && (string) $candidate->getAttribute('user_id') !== (string) $request->userId);
        }

        if ($request->entityType === MatchableEntityType::Guardian) {
            return $this->storedIdentifierConflicts($candidate, 'guardian_number', $guardianNumber)
                || ($request->guardianId !== null
                    && (string) $candidate->getKey() !== (string) $request->guardianId)
                || ($request->userId !== null
                    && (string) $candidate->getAttribute('user_id') !== ''
                    && (string) $candidate->getAttribute('user_id') !== (string) $request->userId);
        }

        if ($request->userId !== null && (string) $candidate->getKey() !== (string) $request->userId) {
            return true;
        }

        if ($request->guardianId === null) {
            return false;
        }

        $guardianUserIds = $this->userIdsForGuardian($schoolId, $request->guardianId);
        if ($guardianUserIds === []) {
            return false;
        }

        foreach ($guardianUserIds as $userId) {
            if ((string) $userId === (string) $candidate->getKey()) {
                return false;
            }
        }

        return true;
    }

    private function matchesStoredIdentifier(Model $candidate, string $column, ?string $expected): bool
    {
        if ($expected === null) {
            return false;
        }

        $stored = $candidate->getAttribute($column);

        return is_string($stored) && $this->normalizer->normalizeIdentifier($stored) === $expected;
    }

    private function storedIdentifierConflicts(Model $candidate, string $column, ?string $expected): bool
    {
        if ($expected === null) {
            return false;
        }

        $stored = $candidate->getAttribute($column);

        return is_string($stored)
            && $stored !== ''
            && $this->normalizer->normalizeIdentifier($stored) !== $expected;
    }

    private function emailMatches(Model $candidate, ?string $email): bool
    {
        if ($email === null) {
            return false;
        }

        $stored = $candidate->getAttribute('email');

        return is_string($stored) && $this->normalizer->normalizeEmail($stored) === $email;
    }

    private function phoneMatches(Model $candidate, MatchableEntityType $entityType, ?string $phone): bool
    {
        if ($phone === null) {
            return false;
        }

        $column = $entityType === MatchableEntityType::User ? 'phone' : 'phone_number';
        $stored = $candidate->getAttribute($column);

        return is_string($stored) && $this->normalizer->normalizePhone($stored) === $phone;
    }

    private function nameMatches(Model $candidate, ?string $firstName, ?string $lastName): bool
    {
        if ($firstName === null || $lastName === null) {
            return false;
        }

        $storedFirst = $candidate->getAttribute('first_name');
        $storedLast = $candidate->getAttribute('last_name');

        if (! is_string($storedFirst) || ! is_string($storedLast)) {
            return false;
        }

        $storedName = $this->normalizer->normalizeName($storedFirst.' '.$storedLast);
        $requestedName = $this->normalizer->normalizeName($firstName.' '.$lastName);

        return $storedName !== null && $storedName === $requestedName;
    }

    private function dateOfBirthMatches(Model $candidate, ?string $dateOfBirth): bool
    {
        if ($dateOfBirth === null) {
            return false;
        }

        $stored = $this->storedDateOfBirth($candidate);

        return $stored !== null && $stored === $dateOfBirth;
    }

    private function storedDateOfBirth(Model $candidate): ?string
    {
        $stored = $candidate->getAttribute('date_of_birth');

        if ($stored === null) {
            return null;
        }

        if ($stored instanceof \Illuminate\Support\Carbon) {
            return $stored->toDateString();
        }

        return is_string($stored) ? $this->normalizer->normalizeDate($stored) : null;
    }

    private function hasSameSchoolCorroboration(
        IdentityMatchRequest $request,
        Model $candidate,
        string $schoolId,
    ): bool {
        if (! $request->sameSchoolCorroboration || $request->entityType !== MatchableEntityType::User) {
            return false;
        }

        $userId = (string) $candidate->getKey();

        $query = StudentGuardianRelationship::query()
            ->forSchool($schoolId)
            ->whereHas('guardian', function (Builder $query) use ($userId): void {
                $query->where('user_id', $userId);
            });

        if ($request->guardianId !== null) {
            $query->where('guardian_id', $request->guardianId);
        }

        if ($request->studentId !== null) {
            $query->where('student_id', $request->studentId);
        }

        return $query->exists();
    }

    /**
     * Digits-only SQL expression for comparison. It does not verify the number.
     */
    private function phoneExpression(string $column): string
    {
        return 'REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(COALESCE('.$column.", ''), ' ', ''), '-', ''), '(', ''), ')', ''), '.', ''), '+', '')";
    }

    /**
     * @param  list<IdentityEvidenceType>  $evidence
     */
    private function conflict(
        IdentityMatchRequest $request,
        IdentityMatchReasonCode $reason,
        array $evidence = [],
    ): IdentityMatchResult {
        return new IdentityMatchResult(
            IdentityMatchOutcome::AmbiguousOrConflict,
            $request->entityType,
            null,
            $evidence,
            [$reason],
            false,
            false,
        );
    }
}
