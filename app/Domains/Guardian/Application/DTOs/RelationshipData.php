<?php

declare(strict_types=1);

namespace App\Domains\Guardian\Application\DTOs;

final readonly class RelationshipData
{
    public function __construct(
        public string $schoolId,
        public int $studentId,
        public int $guardianId,
        public int $relationshipTypeId,
        public bool $isPrimary = false,
        public bool $isEmergencyContact = false,
        public bool $isAuthorizedPickup = false,
        public bool $isFinancialResponsible = false,
        public bool $isNotificationRecipient = false,
        public bool $isMedicalContact = false,
        public ?int $emergencyContactPriority = null,
        public ?string $pickupValidFrom = null,
        public ?string $pickupValidUntil = null,
        public ?string $relationshipStartDate = null,
        public ?string $notes = null,
    ) {
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            schoolId: (string) $data['school_id'],
            studentId: (int) $data['student_id'],
            guardianId: (int) $data['guardian_id'],
            relationshipTypeId: (int) $data['relationship_type_id'],
            isPrimary: (bool) ($data['is_primary'] ?? false),
            isEmergencyContact: (bool) ($data['is_emergency_contact'] ?? false),
            isAuthorizedPickup: (bool) ($data['is_authorized_pickup'] ?? false),
            isFinancialResponsible: (bool) ($data['is_financial_responsible'] ?? false),
            isNotificationRecipient: (bool) ($data['is_notification_recipient'] ?? false),
            isMedicalContact: (bool) ($data['is_medical_contact'] ?? false),
            emergencyContactPriority: isset($data['emergency_contact_priority']) ? (int) $data['emergency_contact_priority'] : null,
            pickupValidFrom: self::nullableString($data['pickup_valid_from'] ?? null),
            pickupValidUntil: self::nullableString($data['pickup_valid_until'] ?? null),
            relationshipStartDate: self::nullableString($data['relationship_start_date'] ?? null),
            notes: self::nullableString($data['notes'] ?? null),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'school_id' => $this->schoolId,
            'student_id' => $this->studentId,
            'guardian_id' => $this->guardianId,
            'relationship_type_id' => $this->relationshipTypeId,
            'is_primary' => $this->isPrimary,
            'is_emergency_contact' => $this->isEmergencyContact,
            'is_authorized_pickup' => $this->isAuthorizedPickup,
            'is_financial_responsible' => $this->isFinancialResponsible,
            'is_notification_recipient' => $this->isNotificationRecipient,
            'is_medical_contact' => $this->isMedicalContact,
            'emergency_contact_priority' => $this->emergencyContactPriority,
            'pickup_valid_from' => $this->pickupValidFrom,
            'pickup_valid_until' => $this->pickupValidUntil,
            'relationship_start_date' => $this->relationshipStartDate,
            'notes' => $this->notes,
        ];
    }

    private static function nullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $trimmed = trim((string) $value);

        return $trimmed === '' ? null : $trimmed;
    }
}
