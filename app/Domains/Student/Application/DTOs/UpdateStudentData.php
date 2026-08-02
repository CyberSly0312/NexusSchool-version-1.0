<?php

declare(strict_types=1);

namespace App\Domains\Student\Application\DTOs;

use App\Domains\Student\Domain\Enums\Gender;
use App\Domains\Student\Domain\ValueObjects\EmergencyContact;
use App\Domains\Student\Domain\ValueObjects\GuardianReference;

/**
 * Represents a mutable update to a student's demographic, contact and academic
 * assignment. Fields that are null keep their current persisted value.
 */
final readonly class UpdateStudentData
{
    /**
     * @param  list<GuardianReference>  $guardians
     */
    public function __construct(
        public ?string $firstName = null,
        public ?string $lastName = null,
        public ?string $middleName = null,
        public ?string $preferredName = null,
        public ?Gender $gender = null,
        public ?string $birthDate = null,
        public ?string $nationality = null,
        public ?string $email = null,
        public ?string $phone = null,
        public ?string $admissionDate = null,
        public ?int $academicCalendarYearId = null,
        public ?int $schoolClassId = null,
        public ?int $classSectionId = null,
        public ?string $nationalId = null,
        public ?string $birthCertificateReference = null,
        public ?string $photoPath = null,
        public ?string $addressStreet = null,
        public ?string $addressCity = null,
        public ?string $addressState = null,
        public ?string $addressPostalCode = null,
        public ?string $addressCountry = null,
        public ?EmergencyContact $emergencyContact = null,
        public array $guardians = [],
        public ?string $medicalNotes = null,
        public bool $replaceGuardians = false,
    ) {
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            firstName: $data['first_name'] ?? null,
            lastName: $data['last_name'] ?? null,
            middleName: $data['middle_name'] ?? null,
            preferredName: $data['preferred_name'] ?? null,
            gender: isset($data['gender']) ? Gender::from($data['gender']) : null,
            birthDate: $data['birth_date'] ?? $data['date_of_birth'] ?? null,
            nationality: $data['nationality'] ?? null,
            email: $data['email'] ?? null,
            phone: $data['phone'] ?? $data['phone_number'] ?? null,
            admissionDate: $data['admission_date'] ?? null,
            academicCalendarYearId: isset($data['academic_calendar_year_id']) ? (int) $data['academic_calendar_year_id'] : null,
            schoolClassId: isset($data['school_class_id']) ? (int) $data['school_class_id'] : null,
            classSectionId: isset($data['class_section_id']) ? (int) $data['class_section_id'] : null,
            nationalId: $data['national_id'] ?? null,
            birthCertificateReference: $data['birth_certificate_reference'] ?? null,
            photoPath: $data['photo_path'] ?? $data['photo'] ?? null,
            addressStreet: $data['address_street'] ?? null,
            addressCity: $data['address_city'] ?? null,
            addressState: $data['address_state'] ?? null,
            addressPostalCode: $data['address_postal_code'] ?? null,
            addressCountry: $data['address_country'] ?? null,
            emergencyContact: EmergencyContact::fromNullable(
                $data['emergency_contact_name'] ?? null,
                $data['emergency_contact_phone'] ?? null,
                $data['emergency_contact_relationship'] ?? null,
            ),
            guardians: $data['guardians'] ?? [],
            medicalNotes: $data['medical_notes'] ?? null,
            replaceGuardians: (bool) ($data['replace_guardians'] ?? false),
        );
    }

    /**
     * @param  list<GuardianReference>  $guardians
     */
    public function withGuardians(array $guardians): self
    {
        return new self(
            firstName: $this->firstName,
            lastName: $this->lastName,
            middleName: $this->middleName,
            preferredName: $this->preferredName,
            gender: $this->gender,
            birthDate: $this->birthDate,
            nationality: $this->nationality,
            email: $this->email,
            phone: $this->phone,
            admissionDate: $this->admissionDate,
            academicCalendarYearId: $this->academicCalendarYearId,
            schoolClassId: $this->schoolClassId,
            classSectionId: $this->classSectionId,
            nationalId: $this->nationalId,
            birthCertificateReference: $this->birthCertificateReference,
            photoPath: $this->photoPath,
            addressStreet: $this->addressStreet,
            addressCity: $this->addressCity,
            addressState: $this->addressState,
            addressPostalCode: $this->addressPostalCode,
            addressCountry: $this->addressCountry,
            emergencyContact: $this->emergencyContact,
            guardians: $guardians,
            medicalNotes: $this->medicalNotes,
            replaceGuardians: true,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'middle_name' => $this->middleName,
            'preferred_name' => $this->preferredName,
            'gender' => $this->gender?->value,
            'date_of_birth' => $this->birthDate,
            'nationality' => $this->nationality,
            'email' => $this->email,
            'phone_number' => $this->phone,
            'admission_date' => $this->admissionDate,
            'academic_calendar_year_id' => $this->academicCalendarYearId,
            'school_class_id' => $this->schoolClassId,
            'class_section_id' => $this->classSectionId,
            'national_id' => $this->nationalId,
            'birth_certificate_reference' => $this->birthCertificateReference,
            'photo' => $this->photoPath,
            'address_street' => $this->addressStreet,
            'address_city' => $this->addressCity,
            'address_state' => $this->addressState,
            'address_postal_code' => $this->addressPostalCode,
            'address_country' => $this->addressCountry,
            'medical_notes' => $this->medicalNotes,
        ];
    }
}
