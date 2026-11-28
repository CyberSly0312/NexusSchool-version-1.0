<?php

declare(strict_types=1);

namespace Database\Factories\Guardian;

use App\Domains\Guardian\Domain\Enums\RelationshipPortalAccessStatus;
use App\Domains\Guardian\Domain\Enums\RelationshipVerificationStatus;
use App\Domains\Guardian\Infrastructure\Models\Guardian;
use App\Domains\Guardian\Infrastructure\Models\RelationshipType;
use App\Domains\Guardian\Infrastructure\Models\StudentGuardianRelationship;
use App\Domains\Student\Infrastructure\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StudentGuardianRelationship>
 */
class StudentGuardianRelationshipFactory extends Factory
{
    protected $model = StudentGuardianRelationship::class;

    public function definition(): array
    {
        return [
            'school_id' => 'fac-'.(string) \Illuminate\Support\Str::ulid(),
            'student_id' => Student::factory(),
            'guardian_id' => Guardian::factory(),
            'relationship_type_id' => RelationshipType::factory(),
            'name' => fn (array $attributes): string => Guardian::find($attributes['guardian_id'])?->full_name ?? fake()->name(),
            'phone' => fn (array $attributes): string => Guardian::find($attributes['guardian_id'])?->phone_number ?? fake()->e164PhoneNumber(),
            'email' => fn (array $attributes): string => Guardian::find($attributes['guardian_id'])?->email ?? fake()->safeEmail(),
            'relationship' => 'guardian',
            'is_primary' => false,
            'is_emergency_contact' => false,
            'is_authorized_pickup' => false,
            'is_financial_responsible' => false,
            'is_notification_recipient' => true,
            'is_medical_contact' => false,
            'emergency_contact_priority' => null,
            'pickup_valid_from' => null,
            'pickup_valid_until' => null,
            'relationship_start_date' => now()->toDateString(),
            'relationship_end_date' => null,
            'notes' => null,
            'verification_status' => RelationshipVerificationStatus::Unverified,
            'portal_access_status' => RelationshipPortalAccessStatus::NotAuthorized,
            'verified_at' => null,
            'portal_authorized_at' => null,
            'portal_revoked_at' => null,
            'verification_evidence_code' => null,
            'authorization_reason_code' => null,
        ];
    }

    public function primary(): static
    {
        return $this->state(fn (): array => ['is_primary' => true]);
    }

    public function emergencyContact(int $priority = 1): static
    {
        return $this->state(fn (): array => [
            'is_emergency_contact' => true,
            'emergency_contact_priority' => $priority,
        ]);
    }

    public function authorizedPickup(): static
    {
        return $this->state(fn (): array => [
            'is_authorized_pickup' => true,
            'pickup_valid_from' => now()->subMonth()->toDateString(),
            'pickup_valid_until' => now()->addYear()->toDateString(),
        ]);
    }

    public function verifiedForPortal(?string $evidenceCode = null): static
    {
        return $this->state(fn (): array => [
            'verification_status' => RelationshipVerificationStatus::Verified,
            'verified_at' => now(),
            'verification_evidence_code' => $evidenceCode,
        ]);
    }

    public function authorizedForPortal(?string $reasonCode = null): static
    {
        return $this->state(fn (): array => [
            'verification_status' => RelationshipVerificationStatus::Verified,
            'portal_access_status' => RelationshipPortalAccessStatus::Authorized,
            'verified_at' => now(),
            'portal_authorized_at' => now(),
            'authorization_reason_code' => $reasonCode,
        ]);
    }

    public function forSchool(int|string $schoolId): static
    {
        return $this->state(fn (): array => ['school_id' => $schoolId]);
    }
}
