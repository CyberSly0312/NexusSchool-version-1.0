<?php

declare(strict_types=1);

namespace App\Domains\Library\DTOs;

readonly class FineData
{
    public function __construct(
        public string $schoolId,
        public int $borrowerId,
        public string $borrowerType = 'student',
        public ?int $bookLoanId = null,
        public string $reason = '',
        public float $amount = 0,
        public ?string $fineDate = null,
        public ?int $processedBy = null,
        public ?string $notes = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            schoolId: $data['school_id'],
            borrowerId: $data['borrower_id'],
            borrowerType: $data['borrower_type'] ?? 'student',
            bookLoanId: $data['book_loan_id'] ?? null,
            reason: $data['reason'] ?? '',
            amount: $data['amount'] ?? 0,
            fineDate: $data['fine_date'] ?? null,
            processedBy: $data['processed_by'] ?? null,
            notes: $data['notes'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'school_id' => $this->schoolId,
            'borrower_id' => $this->borrowerId,
            'borrower_type' => $this->borrowerType,
            'book_loan_id' => $this->bookLoanId,
            'reason' => $this->reason,
            'amount' => $this->amount,
            'fine_date' => $this->fineDate,
            'status' => 'pending',
            'processed_by' => $this->processedBy,
            'notes' => $this->notes,
        ];
    }
}
