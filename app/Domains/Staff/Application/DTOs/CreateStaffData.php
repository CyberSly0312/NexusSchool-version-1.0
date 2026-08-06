<?php

declare(strict_types=1);

namespace App\Domains\Staff\Application\DTOs;

use App\Domains\Staff\Domain\Enums\StaffSpecialization;
use App\Domains\Staff\Domain\Enums\StaffStatus;

/**
 * Data transfer object for registering a Staff aggregate (SPR-006). Teacher,
 * Director, Secretary, ... are specializations, never separate aggregates.
 */
final readonly class CreateStaffData
{
    public function __construct(
        public string $schoolId,
        public string $firstName,
        public string $lastName,
        public ?string $staffNumber = null,
        public ?string $middleName = null,
        public ?string $preferredName = null,
        public ?string $gender = null,
        public ?string $dateOfBirth = null,
        public ?string $nationality = null,
        public ?string $photo = null,
        public ?string $email = null,
        public ?string $phoneNumber = null,
        public ?string $alternatePhone = null,
        public ?string $residentialAddress = null,
        public ?string $nationalId = null,
        public ?string $taxNumber = null,
        public ?string $bankName = null,
        public ?string $bankAccount = null,
        public StaffSpecialization $specialization = StaffSpecialization::Other,
        public ?int $userId = null,
        public StaffStatus $status = StaffStatus::Active,
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
            firstName: (string) $data['first_name'],
            lastName: (string) $data['last_name'],
            staffNumber: self::nullableString($data['staff_number'] ?? null),
            middleName: self::nullableString($data['middle_name'] ?? null),
            preferredName: self::nullableString($data['preferred_name'] ?? null),
            gender: self::nullableString($data['gender'] ?? null),
            dateOfBirth: self::nullableString($data['date_of_birth'] ?? null),
            nationality: self::nullableString($data['nationality'] ?? null),
            photo: self::nullableString($data['photo'] ?? null),
            email: self::nullableString($data['email'] ?? null),
            phoneNumber: self::nullableString($data['phone_number'] ?? null),
            alternatePhone: self::nullableString($data['alternate_phone'] ?? null),
            residentialAddress: self::nullableString($data['residential_address'] ?? null),
            nationalId: self::nullableString($data['national_id'] ?? null),
            taxNumber: self::nullableString($data['tax_number'] ?? null),
            bankName: self::nullableString($data['bank_name'] ?? null),
            bankAccount: self::nullableString($data['bank_account'] ?? null),
            specialization: isset($data['specialization'])
                ? StaffSpecialization::from((string) $data['specialization'])
                : StaffSpecialization::Other,
            userId: isset($data['user_id']) ? (int) $data['user_id'] : null,
            status: isset($data['status']) ? StaffStatus::from((string) $data['status']) : StaffStatus::Active,
            notes: self::nullableString($data['notes'] ?? null),
        );
    }

    public function withStaffNumber(string $staffNumber): self
    {
        return new self(
            schoolId: $this->schoolId,
            firstName: $this->firstName,
            lastName: $this->lastName,
            staffNumber: $staffNumber,
            middleName: $this->middleName,
            preferredName: $this->preferredName,
            gender: $this->gender,
            dateOfBirth: $this->dateOfBirth,
            nationality: $this->nationality,
            photo: $this->photo,
            email: $this->email,
            phoneNumber: $this->phoneNumber,
            alternatePhone: $this->alternatePhone,
            residentialAddress: $this->residentialAddress,
            nationalId: $this->nationalId,
            taxNumber: $this->taxNumber,
            bankName: $this->bankName,
            bankAccount: $this->bankAccount,
            specialization: $this->specialization,
            userId: $this->userId,
            status: $this->status,
            notes: $this->notes,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'school_id' => $this->schoolId,
            'staff_number' => $this->staffNumber,
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'middle_name' => $this->middleName,
            'preferred_name' => $this->preferredName,
            'gender' => $this->gender,
            'date_of_birth' => $this->dateOfBirth,
            'nationality' => $this->nationality,
            'photo' => $this->photo,
            'email' => $this->email,
            'phone_number' => $this->phoneNumber,
            'alternate_phone' => $this->alternatePhone,
            'residential_address' => $this->residentialAddress,
            'national_id' => $this->nationalId,
            'tax_number' => $this->taxNumber,
            'bank_name' => $this->bankName,
            'bank_account' => $this->bankAccount,
            'specialization' => $this->specialization->value,
            'user_id' => $this->userId,
            'status' => $this->status->value,
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
