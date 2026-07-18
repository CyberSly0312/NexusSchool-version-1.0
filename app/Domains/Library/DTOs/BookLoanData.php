<?php

declare(strict_types=1);

namespace App\Domains\Library\DTOs;

readonly class BookLoanData
{
    public function __construct(
        public string $schoolId,
        public int $bookCopyId,
        public int $borrowerId,
        public string $borrowerType = 'student',
        public ?string $loanDate = null,
        public ?string $dueDate = null,
        public ?int $issuedBy = null,
        public ?string $notes = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            schoolId: $data['school_id'],
            bookCopyId: $data['book_copy_id'],
            borrowerId: $data['borrower_id'],
            borrowerType: $data['borrower_type'] ?? 'student',
            loanDate: $data['loan_date'] ?? null,
            dueDate: $data['due_date'] ?? null,
            issuedBy: $data['issued_by'] ?? null,
            notes: $data['notes'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'school_id' => $this->schoolId,
            'book_copy_id' => $this->bookCopyId,
            'borrower_id' => $this->borrowerId,
            'borrower_type' => $this->borrowerType,
            'loan_date' => $this->loanDate,
            'due_date' => $this->dueDate,
            'status' => 'active',
            'issued_by' => $this->issuedBy,
            'notes' => $this->notes,
        ];
    }
}
