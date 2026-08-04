<?php

declare(strict_types=1);

namespace App\Domains\Guardian\Application\DTOs;

use App\Domains\Guardian\Domain\Enums\GuardianStatus;
use App\Domains\Guardian\Domain\Enums\PreferredContactMethod;

final readonly class GuardianData
{
    public function __construct(
        public string $schoolId,
        public string $firstName,
        public string $lastName,
        public ?string $guardianNumber = null,
        public ?string $middleName = null,
        public ?string $preferredName = null,
        public ?string $gender = null,
        public ?string $dateOfBirth = null,
        public ?string $nationality = null,
        public ?string $relationshipType = 'guardian',
        public ?string $email = null,
        public ?string $phoneNumber = null,
        public ?string $alternatePhone = null,
        public ?string $occupation = null,
        public ?string $employer = null,
        public ?string $residentialAddress = null,
        public ?string $postalAddress = null,
        public ?string $preferredCommunicationLanguage = null,
        public PreferredContactMethod $preferredContactMethod = PreferredContactMethod::Phone,
        public ?int $userId = null,
        public ?string $notes = null,
        public GuardianStatus $status = GuardianStatus::Active,
    ) {
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            schoolId: (string) $data['school_id'],
            firstName: (string) $data['first_name'],
            lastName: (string) $data['last_name'],
            guardianNumber: isset($data['guardian_number']) && trim((string) $data['guardian_number']) !== ''
                ? (string) $data['guardian_number']
                : null,
            middleName: self::nullableString($data['middle_name'] ?? null),
            preferredName: self::nullableString($data['preferred_name'] ?? null),
            gender: self::nullableString($data['gender'] ?? null),
            dateOfBirth: self::nullableString($data['date_of_birth'] ?? null),
            nationality: self::nullableString($data['nationality'] ?? null),
            relationshipType: self::nullableString($data['relationship_type'] ?? 'guardian') ?? 'guardian',
            email: self::nullableString($data['email'] ?? null),
            phoneNumber: self::nullableString($data['phone_number'] ?? null),
            alternatePhone: self::nullableString($data['alternate_phone'] ?? null),
            occupation: self::nullableString($data['occupation'] ?? null),
            employer: self::nullableString($data['employer'] ?? null),
            residentialAddress: self::nullableString($data['residential_address'] ?? null),
            postalAddress: self::nullableString($data['postal_address'] ?? null),
            preferredCommunicationLanguage: self::nullableString($data['preferred_communication_language'] ?? null),
            preferredContactMethod: isset($data['preferred_contact_method'])
                ? PreferredContactMethod::from((string) $data['preferred_contact_method'])
                : PreferredContactMethod::Phone,
            userId: isset($data['user_id']) ? (int) $data['user_id'] : null,
            notes: self::nullableString($data['notes'] ?? null),
            status: isset($data['status']) ? GuardianStatus::from((string) $data['status']) : GuardianStatus::Active,
        );
    }

    public function withGuardianNumber(string $guardianNumber): self
    {
        return new self(
            schoolId: $this->schoolId,
            firstName: $this->firstName,
            lastName: $this->lastName,
            guardianNumber: $guardianNumber,
            middleName: $this->middleName,
            preferredName: $this->preferredName,
            gender: $this->gender,
            dateOfBirth: $this->dateOfBirth,
            nationality: $this->nationality,
            relationshipType: $this->relationshipType,
            email: $this->email,
            phoneNumber: $this->phoneNumber,
            alternatePhone: $this->alternatePhone,
            occupation: $this->occupation,
            employer: $this->employer,
            residentialAddress: $this->residentialAddress,
            postalAddress: $this->postalAddress,
            preferredCommunicationLanguage: $this->preferredCommunicationLanguage,
            preferredContactMethod: $this->preferredContactMethod,
            userId: $this->userId,
            notes: $this->notes,
            status: $this->status,
        );
    }

    /**
     * @return array<string, mixed>
     */
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
            'preferred_communication_language' => $this->preferredCommunicationLanguage,
            'preferred_contact_method' => $this->preferredContactMethod->value,
            'user_id' => $this->userId,
            'notes' => $this->notes,
            'status' => $this->status->value,
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
