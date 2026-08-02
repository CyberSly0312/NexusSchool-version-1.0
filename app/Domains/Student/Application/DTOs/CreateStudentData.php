<?php

declare(strict_types=1);

namespace App\Domains\Student\Application\DTOs;

use App\Domains\Student\Domain\Enums\Gender;
use App\Domains\Student\Domain\Enums\StudentStatus;
use App\Domains\Student\Domain\Enums\TransferStatus;
use App\Domains\Student\Domain\ValueObjects\EmergencyContact;
use App\Domains\Student\Domain\ValueObjects\GuardianReference;

final readonly class CreateStudentData
{
    /**
     * @param  list<GuardianReference>  $guardians
     */
    public function __construct(
        public string $schoolId,
        public string $firstName,
        public string $lastName,
        public ?string $studentNumber = null,
        public ?string $middleName = null,
        public ?string $preferredName = null,
        public ?Gender $gender = null,
        public ?string $birthDate = null,
        public ?string $nationality = null,
        public ?string $email = null,
        public ?string $phone = null,
        public ?string $admissionDate = null,
        public ?string $admissionNumber = null,
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
        public StudentStatus $studentStatus = StudentStatus::Active,
        public TransferStatus $transferStatus = TransferStatus::None,
        public ?string $transferFromSchool = null,
    ) {
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            schoolId: $data['school_id'],
            firstName: $data['first_name'],
            lastName: $data['last_name'],
            studentNumber: $data['student_number'] ?? null,
            middleName: $data['middle_name'] ?? null,
            preferredName: $data['preferred_name'] ?? null,
            gender: isset($data['gender']) ? Gender::from($data['gender']) : null,
            birthDate: $data['birth_date'] ?? $data['date_of_birth'] ?? null,
            nationality: $data['nationality'] ?? null,
            email: $data['email'] ?? null,
            phone: $data['phone'] ?? $data['phone_number'] ?? null,
            admissionDate: $data['admission_date'] ?? null,
            admissionNumber: $data['admission_number'] ?? null,
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
            studentStatus: isset($data['status']) ? StudentStatus::from($data['status']) : StudentStatus::Active,
            transferStatus: isset($data['transfer_status']) ? TransferStatus::from($data['transfer_status']) : TransferStatus::None,
            transferFromSchool: $data['transfer_from_school'] ?? null,
        );
    }

    /**
     * @param  list<GuardianReference>  $guardians
     */
    public function withGuardians(array $guardians): self
    {
        return new self(
            schoolId: $this->schoolId,
            firstName: $this->firstName,
            lastName: $this->lastName,
            studentNumber: $this->studentNumber,
            middleName: $this->middleName,
            preferredName: $this->preferredName,
            gender: $this->gender,
            birthDate: $this->birthDate,
            nationality: $this->nationality,
            email: $this->email,
            phone: $this->phone,
            admissionDate: $this->admissionDate,
            admissionNumber: $this->admissionNumber,
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
            studentStatus: $this->studentStatus,
            transferStatus: $this->transferStatus,
            transferFromSchool: $this->transferFromSchool,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'school_id' => $this->schoolId,
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'student_number' => $this->studentNumber,
            'middle_name' => $this->middleName,
            'preferred_name' => $this->preferredName,
            'gender' => $this->gender?->value,
            'date_of_birth' => $this->birthDate,
            'nationality' => $this->nationality,
            'email' => $this->email,
            'phone_number' => $this->phone,
            'admission_date' => $this->admissionDate,
            'admission_number' => $this->admissionNumber,
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
            'status' => $this->studentStatus->value,
            'transfer_status' => $this->transferStatus->value,
            'transfer_from_school' => $this->transferFromSchool,
        ];
    }
}
