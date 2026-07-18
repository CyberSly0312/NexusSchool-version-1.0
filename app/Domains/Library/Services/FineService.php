<?php

declare(strict_types=1);

namespace App\Domains\Library\Services;

use App\Domains\Library\DTOs\FineData;
use App\Domains\Library\Enums\BookLoanStatus;
use App\Domains\Library\Enums\FineStatus;
use App\Domains\Library\Events\FineGenerated;
use App\Domains\Library\Events\FinePaid;
use App\Domains\Library\Rules\FineRules;
use App\Models\BookLoan;
use App\Models\Fine;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FineService
{
    public function __construct(
        protected Fine $fineModel = new Fine(),
    ) {
    }

    public function createFine(FineData $data): Fine
    {
        $validated = FineRules::validate($data->toArray(), 'create');

        return DB::transaction(function () use ($validated) {
            $fine = $this->fineModel->create(array_merge($validated, [
                'fine_date' => $validated['fine_date'] ?? now()->format('Y-m-d'),
            ]));

            FineGenerated::dispatch($fine->fresh());

            return $fine;
        });
    }

    public function payFine(Fine $fine, float $amount, ?string $paymentMethod = null, ?string $referenceNumber = null): Fine
    {
        if ($fine->status === FineStatus::Paid || $fine->status === FineStatus::Waived) {
            throw ValidationException::withMessages([
                'status' => 'This fine has already been settled.',
            ]);
        }

        if ($amount <= 0) {
            throw ValidationException::withMessages([
                'amount' => 'Payment amount must be greater than zero.',
            ]);
        }

        if ($amount > $fine->balance) {
            throw ValidationException::withMessages([
                'amount' => 'Payment amount exceeds the outstanding balance.',
            ]);
        }

        return DB::transaction(function () use ($fine, $amount, $paymentMethod, $referenceNumber) {
            $newPaidAmount = (float) $fine->paid_amount + $amount;
            $status = $newPaidAmount >= (float) $fine->amount
                ? FineStatus::Paid
                : FineStatus::PartiallyPaid;

            $fine->update([
                'paid_amount' => $newPaidAmount,
                'paid_date' => now()->format('Y-m-d'),
                'payment_method' => $paymentMethod,
                'reference_number' => $referenceNumber,
                'status' => $status,
            ]);

            FinePaid::dispatch($fine->fresh());

            return $fine->refresh();
        });
    }

    public function waiveFine(Fine $fine, ?int $processedBy = null, ?string $notes = null): Fine
    {
        if ($fine->status === FineStatus::Paid || $fine->status === FineStatus::Waived) {
            throw ValidationException::withMessages([
                'status' => 'This fine has already been settled.',
            ]);
        }

        return DB::transaction(function () use ($fine, $processedBy, $notes) {
            $fine->update([
                'waived_amount' => $fine->amount - $fine->paid_amount,
                'waived_date' => now()->format('Y-m-d'),
                'status' => FineStatus::Waived,
                'processed_by' => $processedBy,
                'notes' => $notes,
            ]);

            FinePaid::dispatch($fine->fresh());

            return $fine->refresh();
        });
    }

    public function updateFine(Fine $fine, array $data): Fine
    {
        $validated = FineRules::validate($data, 'update', $fine->id);

        $fine->update($validated);

        return $fine->refresh();
    }

    public function deleteFine(Fine $fine): bool
    {
        $fine->delete();

        return true;
    }

    public function findFineById(int $id): Fine
    {
        return $this->fineModel->with(['bookLoan.bookCopy.book', 'borrower'])->findOrFail($id);
    }

    public function paginateFines(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->fineModel->newQuery()->with(['bookLoan.bookCopy.book', 'borrower']);

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where('reason', 'like', "%{$search}%");
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

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function generateOverdueFines(string $schoolId, float $finePerDay = 1.0): int
    {
        $overdueLoans = BookLoan::where('school_id', $schoolId)
            ->where('status', BookLoanStatus::Active)
            ->where('due_date', '<', now())
            ->get();

        $count = 0;

        foreach ($overdueLoans as $loan) {
            $daysOverdue = $loan->due_date->diffInDays(now());
            $existingFine = $this->fineModel
                ->where('book_loan_id', $loan->id)
                ->whereNotIn('status', [FineStatus::Paid, FineStatus::Waived])
                ->first();

            if ($existingFine) {
                $existingFine->update(['amount' => $daysOverdue * $finePerDay]);
            } else {
                $this->fineModel->create([
                    'school_id' => $schoolId,
                    'book_loan_id' => $loan->id,
                    'borrower_id' => $loan->borrower_id,
                    'borrower_type' => $loan->borrower_type,
                    'reason' => "Overdue book: {$daysOverdue} days late",
                    'amount' => $daysOverdue * $finePerDay,
                    'fine_date' => now()->format('Y-m-d'),
                ]);

                $count++;
            }
        }

        return $count;
    }

    public function getBorrowerOutstandingFines(int $borrowerId, string $borrowerType): float
    {
        return (float) $this->fineModel
            ->where('borrower_id', $borrowerId)
            ->where('borrower_type', $borrowerType)
            ->unpaid()
            ->sum(DB::raw('amount - paid_amount - waived_amount'));
    }
}
