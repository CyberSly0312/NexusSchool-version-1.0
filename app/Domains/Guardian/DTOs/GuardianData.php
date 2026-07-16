<?php

namespace App\Domains\Guardian\DTOs;

readonly class GuardianData
{
    public function __construct(
        public string $schoolId,
        public string $guardianNumber,
        public string $firstName,
        public string $lastName,
        public ?string $middleName,
        public ?string $preferredName,
        public string $gender,
        public ?string $dateOfBirth,
        public ?string $nationality,
        public string $relationshipType,
        public ?string $email,
        public string $phoneNumber,
        public ?string $alternatePhone = null,
        public ?string $occupation = null,
        public ?string $employer = null,
        public ?string $residentialAddress = null,
        public ?string $postalAddress = null,
        public ?string $emergencyContact = null,
        public ?string $preferredCommunicationLanguage = null,
        public string $preferredContactMethod = 'phone',
        public bool $isPrimaryGuardian = false,
        public ?int $userId = null,
        public ?string $notes = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            schoolId: $data['school_id'],
            guardianNumber: $data['guardian_number'],
            firstName: $data['first_name'],
            lastName: $data['last_name'],
            middleName: $data['middle_name'] ?? null,
            preferredName: $data['preferred_name'] ?? null,
            gender: $data['gender'] ?? 'other',
            dateOfBirth: $data['date_of_birth'] ?? null,
            nationality: $data['nationality'] ?? null,
            relationshipType: $data['relationship_type'] ?? 'other',
            email: $data['email'] ?? null,
            phoneNumber: $data['phone_number'],
            alternatePhone: $data['alternate_phone'] ?? null,
            occupation: $data['occupation'] ?? null,
            employer: $data['employer'] ?? null,
            residentialAddress: $data['residential_address'] ?? null,
            postalAddress: $data['postal_address'] ?? null,
            emergencyContact: $data['emergency_contact'] ?? null,
            preferredCommunicationLanguage: $data['preferred_communication_language'] ?? null,
            preferredContactMethod: $data['preferred_contact_method'] ?? 'phone',
            isPrimaryGuardian: $data['is_primary_guardian'] ?? false,
            userId: $data['user_id'] ?? null,
            notes: $data['notes'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'school_id' => $this->schoolId,
            'guardian_number' => $this->guardianNumber,
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'middle_name' => $this->middleName,
            'preferred_name' => $this->preferredName,
            'gender' => $this->gender,
            'date_of_birth' => $this->dateOfBirth,
            'nationality' => $this->nationality,
            'relationship_type' => $this->relationshipType,
            'email' => $this->email,
            'phone_number' => $this->phoneNumber,
            'alternate_phone' => $this->alternatePhone,
            'occupation' => $this->occupation,
            'employer' => $this->employer,
            'residential_address' => $this->residentialAddress,
            'postal_address' => $this->postalAddress,
            'emergency_contact' => $this->emergencyContact,
            'preferred_communication_language' => $this->preferredCommunicationLanguage,
            'preferred_contact_method' => $this->preferredContactMethod,
            'is_primary_guardian' => $this->isPrimaryGuardian,
            'user_id' => $this->userId,
            'notes' => $this->notes,
            'status' => 'active',
        ];
    }
}
