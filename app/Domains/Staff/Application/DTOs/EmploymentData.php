<?php

declare(strict_types=1);

namespace App\Domains\Staff\Application\DTOs;

use App\Domains\Staff\Domain\Enums\ContractType;
use App\Domains\Staff\Domain\Enums\EmploymentStatus;
use App\Domains\Staff\Domain\Enums\EmploymentType;

/**
 * Data transfer object for starting an employment contract for a staff member.
 */
final readonly class EmploymentData
{
    public function __construct(
        public string $schoolId,
        public int $staffId,
        public string $contractStartDate,
        public ?int $positionId = null,
        public ?int $departmentId = null,
        public EmploymentType $employmentType = EmploymentType::FullTime,
        public ContractType $contractType = ContractType::Permanent,
        public EmploymentStatus $status = EmploymentStatus::Active,
        public ?string $salaryScale = null,
        public int|float|string|null $salaryAmount = null,
        public ?string $contractEndDate = null,
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
            staffId: (int) $data['staff_id'],
            contractStartDate: (string) $data['contract_start_date'],
            positionId: isset($data['position_id']) ? (int) $data['position_id'] : null,
            departmentId: isset($data['department_id']) ? (int) $data['department_id'] : null,
            employmentType: isset($data['employment_type'])
                ? EmploymentType::from((string) $data['employment_type'])
                : EmploymentType::FullTime,
            contractType: isset($data['contract_type'])
                ? ContractType::from((string) $data['contract_type'])
                : ContractType::Permanent,
            status: isset($data['status'])
                ? EmploymentStatus::from((string) $data['status'])
                : EmploymentStatus::Active,
            salaryScale: self::nullableString($data['salary_scale'] ?? null),
            salaryAmount: $data['salary_amount'] ?? null,
            contractEndDate: self::nullableString($data['contract_end_date'] ?? null),
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
            'staff_id' => $this->staffId,
            'position_id' => $this->positionId,
            'department_id' => $this->departmentId,
            'employment_type' => $this->employmentType->value,
            'contract_type' => $this->contractType->value,
            'status' => $this->status->value,
            'salary_scale' => $this->salaryScale,
            'salary_amount' => $this->salaryAmount,
            'contract_start_date' => $this->contractStartDate,
            'contract_end_date' => $this->contractEndDate,
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
