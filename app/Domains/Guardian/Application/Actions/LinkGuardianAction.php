<?php

declare(strict_types=1);

namespace App\Domains\Guardian\Application\Actions;

use App\Domains\Guardian\Application\DTOs\RelationshipData;
use App\Domains\Guardian\Application\Services\GuardianLinkProvenance;
use App\Domains\Guardian\Domain\Enums\GuardianAccessDenialReason;
use App\Domains\Guardian\Domain\Events\GuardianLinked;
use App\Domains\Guardian\Domain\Events\PrimaryGuardianAssigned;
use App\Domains\Guardian\Domain\Exceptions\DuplicateRelationshipException;
use App\Domains\Guardian\Domain\Exceptions\GuardianRelationshipVerificationException;
use App\Domains\Guardian\Domain\Exceptions\GuardianStateException;
use App\Domains\Guardian\Domain\Exceptions\RelationshipStateException;
use App\Domains\Guardian\Infrastructure\Models\Guardian;
use App\Domains\Guardian\Infrastructure\Models\RelationshipType;
use App\Domains\Guardian\Infrastructure\Models\StudentGuardianRelationship;
use App\Domains\School\Models\School;
use App\Domains\Student\Infrastructure\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

final class LinkGuardianAction
{
    public function execute(RelationshipData $data): StudentGuardianRelationship
    {
        $actor = $this->resolveActor();
        $school = $this->resolveSchool();
        $this->assertTenantMatchesData($school, $data);
        $this->assertActorSchool($actor, $school);

        $guardian = $this->resolveGuardian($data);
        $student = $this->resolveStudent($data);
        $this->resolveRelationshipType($data);
        $this->authorizeLink($actor, $guardian);
        $this->assertNoExistingRelationship($data);

        return DB::transaction(function () use ($data, $guardian, $student, $actor): StudentGuardianRelationship {
            if ($data->isPrimary) {
                StudentGuardianRelationship::query()
                    ->forSchool($data->schoolId)
                    ->forStudent($student->getKey())
                    ->update(['is_primary' => false]);
            }

            $relationship = StudentGuardianRelationship::create([
                'school_id' => $data->schoolId,
                'student_id' => $student->getKey(),
                'guardian_id' => $guardian->getKey(),
                'relationship_type_id' => $data->relationshipTypeId,
                'name' => $guardian->full_name,
                'phone' => $guardian->phone_number,
                'email' => $guardian->email,
                'relationship' => $this->relationshipTypeCode($data),
                'is_primary' => $data->isPrimary,
                'is_emergency_contact' => $data->isEmergencyContact,
                'is_authorized_pickup' => $data->isAuthorizedPickup,
                'is_financial_responsible' => $data->isFinancialResponsible,
                'is_notification_recipient' => $data->isNotificationRecipient,
                'is_medical_contact' => $data->isMedicalContact,
                'emergency_contact_priority' => $data->emergencyContactPriority,
                'pickup_valid_from' => $data->pickupValidFrom,
                'pickup_valid_until' => $data->pickupValidUntil,
                'relationship_start_date' => $data->relationshipStartDate ?? now()->toDateString(),
                'notes' => $data->notes,
            ]);

            GuardianLinkProvenance::issueFor($relationship, $actor);
            GuardianLinked::dispatch($relationship);

            if ($data->isPrimary) {
                PrimaryGuardianAssigned::dispatch($relationship, $actor);
            }

            return $relationship;
        });
    }

    private function resolveActor(): User
    {
        $actor = Auth::user();

        if (! $actor instanceof User) {
            $this->deny(GuardianAccessDenialReason::ActorNotAuthenticated);
        }

        if ($actor->trashed() || ! $actor->isActive()) {
            $this->deny(GuardianAccessDenialReason::ActorNotActive);
        }

        if ($actor->hasRole('parent')) {
            $this->deny(GuardianAccessDenialReason::ActorNotAuthorized);
        }

        return $actor;
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

    private function assertTenantMatchesData(School $school, RelationshipData $data): void
    {
        if ((string) $data->schoolId !== (string) $school->getKey()) {
            $this->deny(GuardianAccessDenialReason::TenantContextMismatch);
        }
    }

    private function assertActorSchool(User $actor, School $school): void
    {
        if ((string) $actor->school_id !== (string) $school->getKey()) {
            $this->deny(GuardianAccessDenialReason::ActorSchoolMismatch);
        }
    }

    private function authorizeLink(User $actor, Guardian $guardian): void
    {
        if (! Gate::forUser($actor)->allows('link', $guardian)) {
            $this->deny(GuardianAccessDenialReason::ActorNotAuthorized);
        }
    }

    private function resolveGuardian(RelationshipData $data): Guardian
    {
        $guardian = Guardian::query()
            ->forSchool($data->schoolId)
            ->find($data->guardianId);

        if ($guardian === null) {
            throw RelationshipStateException::message('The guardian does not belong to this school.');
        }

        if ($guardian->isImmutable()) {
            throw GuardianStateException::immutable($guardian->id);
        }

        return $guardian;
    }

    private function resolveStudent(RelationshipData $data): Student
    {
        $student = Student::query()
            ->forSchool($data->schoolId)
            ->find($data->studentId);

        if ($student === null || $student->trashed()) {
            throw RelationshipStateException::message('The student does not belong to this school.');
        }

        return $student;
    }

    private function resolveRelationshipType(RelationshipData $data): void
    {
        $exists = RelationshipType::query()
            ->forSchool($data->schoolId)
            ->whereKey($data->relationshipTypeId)
            ->exists();

        if (! $exists) {
            throw RelationshipStateException::message('The relationship type does not belong to this school.');
        }
    }

    private function assertNoExistingRelationship(RelationshipData $data): void
    {
        $exists = StudentGuardianRelationship::query()
            ->forSchool($data->schoolId)
            ->forStudent($data->studentId)
            ->forGuardian($data->guardianId)
            ->exists();

        if ($exists) {
            throw DuplicateRelationshipException::existing($data->studentId, $data->guardianId);
        }
    }

    private function relationshipTypeCode(RelationshipData $data): string
    {
        $type = RelationshipType::query()
            ->forSchool($data->schoolId)
            ->find($data->relationshipTypeId);

        return $type instanceof RelationshipType ? $type->code : 'other';
    }

    private function deny(GuardianAccessDenialReason $reason): never
    {
        throw GuardianRelationshipVerificationException::because($reason);
    }
}
