<?php

declare(strict_types=1);

namespace App\Domains\Library\Services;

use App\Domains\Library\DTOs\BookLoanData;
use App\Domains\Library\DTOs\ReservationData;
use App\Domains\Library\Enums\BookCopyStatus;
use App\Domains\Library\Enums\BookLoanStatus;
use App\Domains\Library\Enums\ReservationStatus;
use App\Domains\Library\Events\BookBorrowed;
use App\Domains\Library\Events\BookReturned;
use App\Domains\Library\Events\ReservationCreated;
use App\Domains\Library\Events\ReservationExpired;
use App\Domains\Library\Rules\BookLoanRules;
use App\Domains\Library\Rules\ReservationRules;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\BookLoan;
use App\Models\Reservation;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CirculationService
{
    public function __construct(
        protected BookLoan $loanModel = new BookLoan(),
        protected Reservation $reservationModel = new Reservation(),
    ) {
    }

    /*
    |--------------------------------------------------------------------------
    | Loan Operations
    |--------------------------------------------------------------------------
    */

    public function borrowBook(BookLoanData $data): BookLoan
    {
        $validated = BookLoanRules::validate($data->toArray(), 'create');

        $this->validateCopyAvailable($validated['book_copy_id']);
        $this->validateNoDuplicateLoan($validated['book_copy_id']);
        $this->validateBorrowerNotAtLimit($validated['borrower_id'], $validated['borrower_type']);

        return DB::transaction(function () use ($validated) {
            $loan = $this->loanModel->create(array_merge($validated, [
                'loan_date' => $validated['loan_date'] ?? now()->format('Y-m-d'),
                'due_date' => $validated['due_date'] ?? now()->addDays(14)->format('Y-m-d'),
                'status' => $validated['status'] ?? BookLoanStatus::Active,
            ]));

            $copy = BookCopy::find($validated['book_copy_id']);
            $copy->update([
                'status' => BookCopyStatus::Borrowed,
                'is_available' => false,
            ]);

            // Fulfill any pending reservation for this copy
            $this->fulfillPendingReservation($copy->book_id, $validated['borrower_id'], $validated['borrower_type']);

            BookBorrowed::dispatch($loan->fresh());

            return $loan;
        });
    }

    public function returnBook(BookLoan $loan, ?int $returnedTo = null): BookLoan
    {
        if ($loan->status !== BookLoanStatus::Active) {
            throw ValidationException::withMessages([
                'status' => 'Only active loans can be returned.',
            ]);
        }

        return DB::transaction(function () use ($loan, $returnedTo) {
            $loan->update([
                'status' => BookLoanStatus::Returned,
                'return_date' => now()->format('Y-m-d'),
                'returned_to' => $returnedTo,
            ]);

            $copy = $loan->bookCopy;
            $copy->update([
                'status' => BookCopyStatus::Returned,
                'is_available' => true,
            ]);

            // Check if there's a pending reservation and set copy to reserved
            $pendingReservation = $this->reservationModel
                ->where('book_id', $copy->book_id)
                ->where('status', ReservationStatus::Pending)
                ->where('expiry_date', '>=', now())
                ->orderBy('reservation_date')
                ->first();

            if ($pendingReservation) {
                $copy->update([
                    'status' => BookCopyStatus::Reserved,
                    'is_available' => false,
                ]);
            }

            BookReturned::dispatch($loan->fresh());

            return $loan->refresh();
        });
    }

    public function markLost(BookLoan $loan): BookLoan
    {
        if ($loan->status !== BookLoanStatus::Active && $loan->status !== BookLoanStatus::Overdue) {
            throw ValidationException::withMessages([
                'status' => 'Only active or overdue loans can be marked as lost.',
            ]);
        }

        $loan->update(['status' => BookLoanStatus::Lost]);

        $loan->bookCopy->update([
            'status' => BookCopyStatus::Archived,
            'is_available' => false,
        ]);

        return $loan->refresh();
    }

    public function renewLoan(BookLoan $loan, ?int $additionalDays = 14): BookLoan
    {
        if ($loan->status !== BookLoanStatus::Active) {
            throw ValidationException::withMessages([
                'status' => 'Only active loans can be renewed.',
            ]);
        }

        if ($loan->renewal_count >= 3) {
            throw ValidationException::withMessages([
                'renewal_count' => 'This loan has reached the maximum number of renewals.',
            ]);
        }

        $loan->update([
            'due_date' => $loan->due_date->addDays($additionalDays),
            'renewal_count' => $loan->renewal_count + 1,
        ]);

        return $loan->refresh();
    }

    public function findLoanById(int $id): BookLoan
    {
        return $this->loanModel->with(['bookCopy.book', 'borrower'])->findOrFail($id);
    }

    public function paginateLoans(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->loanModel->newQuery()->with(['bookCopy.book', 'borrower']);

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('bookCopy', function ($q) use ($search) {
                $q->where('barcode', 'like', "%{$search}%");
            });
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['school_id'])) {
            $query->where('school_id', $filters['school_id']);
        }

        if (isset($filters['borrower_id']) && isset($filters['borrower_type'])) {
            $query->where('borrower_id', $filters['borrower_id'])
                ->where('borrower_type', $filters['borrower_type']);
        }

        if (isset($filters['overdue'])) {
            $query->where('status', BookLoanStatus::Active)
                ->where('due_date', '<', now());
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function getOverdueLoans(string $schoolId): Collection
    {
        return $this->loanModel
            ->where('school_id', $schoolId)
            ->where('status', BookLoanStatus::Active)
            ->where('due_date', '<', now())
            ->with(['bookCopy.book', 'borrower'])
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | Reservation Operations
    |--------------------------------------------------------------------------
    */

    public function createReservation(ReservationData $data): Reservation
    {
        $validated = ReservationRules::validate($data->toArray(), 'create');

        $this->validateNoDuplicatePendingReservation($validated['book_id'], $validated['borrower_id'], $validated['borrower_type']);
        $this->validateBookExists($validated['book_id']);

        return DB::transaction(function () use ($validated) {
            $reservation = $this->reservationModel->create(array_merge($validated, [
                'reservation_date' => $validated['reservation_date'] ?? now()->format('Y-m-d'),
                'expiry_date' => $validated['expiry_date'] ?? now()->addDays(7)->format('Y-m-d'),
                'status' => $validated['status'] ?? ReservationStatus::Pending,
            ]));

            ReservationCreated::dispatch($reservation->fresh());

            return $reservation;
        });
    }

    public function fulfillReservation(Reservation $reservation): Reservation
    {
        if ($reservation->status !== ReservationStatus::Pending) {
            throw ValidationException::withMessages([
                'status' => 'Only pending reservations can be fulfilled.',
            ]);
        }

        $reservation->update([
            'status' => ReservationStatus::Fulfilled,
            'fulfilled_date' => now()->format('Y-m-d'),
        ]);

        return $reservation->refresh();
    }

    public function cancelReservation(Reservation $reservation): Reservation
    {
        if ($reservation->status !== ReservationStatus::Pending) {
            throw ValidationException::withMessages([
                'status' => 'Only pending reservations can be cancelled.',
            ]);
        }

        $reservation->update(['status' => ReservationStatus::Cancelled]);

        return $reservation->refresh();
    }

    public function expireReservations(): int
    {
        $expired = $this->reservationModel
            ->where('status', ReservationStatus::Pending)
            ->where('expiry_date', '<', now())
            ->get();

        $count = 0;

        foreach ($expired as $reservation) {
            $reservation->update(['status' => ReservationStatus::Expired]);

            ReservationExpired::dispatch($reservation->fresh());

            $count++;
        }

        return $count;
    }

    public function findReservationById(int $id): Reservation
    {
        return $this->reservationModel->with(['book', 'borrower'])->findOrFail($id);
    }

    public function paginateReservations(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->reservationModel->newQuery()->with(['book', 'borrower']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['school_id'])) {
            $query->where('school_id', $filters['school_id']);
        }

        if (isset($filters['book_id'])) {
            $query->where('book_id', $filters['book_id']);
        }

        if (isset($filters['borrower_id']) && isset($filters['borrower_type'])) {
            $query->where('borrower_id', $filters['borrower_id'])
                ->where('borrower_type', $filters['borrower_type']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /*
    |--------------------------------------------------------------------------
    | Private Validation Helpers
    |--------------------------------------------------------------------------
    */

    private function validateCopyAvailable(int $bookCopyId): void
    {
        $copy = BookCopy::find($bookCopyId);

        if (! $copy) {
            throw ValidationException::withMessages([
                'book_copy_id' => 'Book copy not found.',
            ]);
        }

        if (! $copy->isAvailable()) {
            throw ValidationException::withMessages([
                'book_copy_id' => 'This book copy is not available for borrowing.',
            ]);
        }
    }

    private function validateNoDuplicateLoan(int $bookCopyId): void
    {
        $activeLoan = $this->loanModel
            ->where('book_copy_id', $bookCopyId)
            ->where('status', BookLoanStatus::Active)
            ->exists();

        if ($activeLoan) {
            throw ValidationException::withMessages([
                'book_copy_id' => 'This book copy already has an active loan.',
            ]);
        }
    }

    private function validateBorrowerNotAtLimit(int $borrowerId, string $borrowerType): void
    {
        $activeLoans = $this->loanModel
            ->where('borrower_id', $borrowerId)
            ->where('borrower_type', $borrowerType)
            ->where('status', BookLoanStatus::Active)
            ->count();

        $maxLoans = 5;

        if ($activeLoans >= $maxLoans) {
            throw ValidationException::withMessages([
                'borrower_id' => "Borrower has reached the maximum of {$maxLoans} active loans.",
            ]);
        }
    }

    private function validateNoDuplicatePendingReservation(int $bookId, int $borrowerId, string $borrowerType): void
    {
        $existing = $this->reservationModel
            ->where('book_id', $bookId)
            ->where('borrower_id', $borrowerId)
            ->where('borrower_type', $borrowerType)
            ->where('status', ReservationStatus::Pending)
            ->exists();

        if ($existing) {
            throw ValidationException::withMessages([
                'book_id' => 'You already have a pending reservation for this book.',
            ]);
        }
    }

    private function validateBookExists(int $bookId): void
    {
        $book = Book::find($bookId);

        if (! $book) {
            throw ValidationException::withMessages([
                'book_id' => 'Book not found.',
            ]);
        }
    }

    private function fulfillPendingReservation(int $bookId, int $borrowerId, string $borrowerType): void
    {
        $reservation = $this->reservationModel
            ->where('book_id', $bookId)
            ->where('borrower_id', $borrowerId)
            ->where('borrower_type', $borrowerType)
            ->where('status', ReservationStatus::Pending)
            ->first();

        if ($reservation) {
            $reservation->update([
                'status' => ReservationStatus::Fulfilled,
                'fulfilled_date' => now()->format('Y-m-d'),
            ]);
        }
    }
}
