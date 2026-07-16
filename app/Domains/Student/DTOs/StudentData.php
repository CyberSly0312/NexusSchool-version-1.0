<?php

namespace App\Domains\Student\DTOs;

readonly class StudentData
{
    public function __construct(
        public string $schoolId,
        public string $studentNumber,
        public ?string $admissionNumber,
        public ?string $nationalId,
        public string $firstName,
        public string $lastName,
        public ?string $middleName,
        public ?string $preferredName,
        public string $gender,
        public string $dateOfBirth,
        public ?string $placeOfBirth = null,
        public ?string $nationality = null,
        public ?string $bloodGroup = null,
        public ?string $religion = null,
        public ?string $photo = null,
        public ?string $email = null,
        public ?string $phoneNumber = null,
        public ?string $residentialAddress = null,
        public ?string $admissionDate = null,
        public ?string $allergies = null,
        public ?string $medicalNotes = null,
        public ?int $userId = null,
        public ?string $notes = null,
        public string $status = 'active',
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            schoolId: $data['school_id'],
            studentNumber: $data['student_number'],
            admissionNumber: $data['admission_number'] ?? null,
            nationalId: $data['national_id'] ?? null,
            firstName: $data['first_name'],
            lastName: $data['last_name'],
            middleName: $data['middle_name'] ?? null,
            preferredName: $data['preferred_name'] ?? null,
            gender: $data['gender'] ?? 'other',
            dateOfBirth: $data['date_of_birth'],
            placeOfBirth: $data['place_of_birth'] ?? null,
            nationality: $data['nationality'] ?? null,
            bloodGroup: $data['blood_group'] ?? null,
            religion: $data['religion'] ?? null,
            photo: $data['photo'] ?? null,
            email: $data['email'] ?? null,
            phoneNumber: $data['phone_number'] ?? null,
            residentialAddress: $data['residential_address'] ?? null,
            admissionDate: $data['admission_date'] ?? null,
            allergies: $data['allergies'] ?? null,
            medicalNotes: $data['medical_notes'] ?? null,
            userId: $data['user_id'] ?? null,
            notes: $data['notes'] ?? null,
            status: $data['status'] ?? 'active',
        );
    }

    public function toArray(): array
    {
        return [
            'school_id' => $this->schoolId,
            'student_number' => $this->studentNumber,
            'admission_number' => $this->admissionNumber,
            'national_id' => $this->nationalId,
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'middle_name' => $this->middleName,
            'preferred_name' => $this->preferredName,
            'gender' => $this->gender,
            'date_of_birth' => $this->dateOfBirth,
            'place_of_birth' => $this->placeOfBirth,
            'nationality' => $this->nationality,
            'blood_group' => $this->bloodGroup,
            'religion' => $this->religion,
            'photo' => $this->photo,
            'email' => $this->email,
            'phone_number' => $this->phoneNumber,
            'residential_address' => $this->residentialAddress,
            'admission_date' => $this->admissionDate,
            'allergies' => $this->allergies,
            'medical_notes' => $this->medicalNotes,
            'user_id' => $this->userId,
            'notes' => $this->notes,
            'status' => $this->status,
        ];
    }
}
