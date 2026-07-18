<?php

declare(strict_types=1);

namespace App\Domains\Library\DTOs;

readonly class ReservationData
{
    public function __construct(
        public string $schoolId,
        public int $bookId,
        public int $borrowerId,
        public string $borrowerType = 'student',
        public ?string $reservationDate = null,
        public ?string $expiryDate = null,
        public ?string $notes = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            schoolId: $data['school_id'],
            bookId: $data['book_id'],
            borrowerId: $data['borrower_id'],
            borrowerType: $data['borrower_type'] ?? 'student',
            reservationDate: $data['reservation_date'] ?? null,
            expiryDate: $data['expiry_date'] ?? null,
            notes: $data['notes'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'school_id' => $this->schoolId,
            'book_id' => $this->bookId,
            'borrower_id' => $this->borrowerId,
            'borrower_type' => $this->borrowerType,
            'reservation_date' => $this->reservationDate,
            'expiry_date' => $this->expiryDate,
            'status' => 'pending',
            'notes' => $this->notes,
        ];
    }
}
