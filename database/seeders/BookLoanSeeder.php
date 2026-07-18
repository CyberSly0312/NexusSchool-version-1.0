<?php

namespace Database\Seeders;

use App\Domains\School\Models\School;
use App\Models\BookCopy;
use App\Models\BookLoan;
use Illuminate\Database\Seeder;

class BookLoanSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::where('slug', 'nexus-demo')->first();
        if (! $school) {
            return;
        }

        $availableCopies = BookCopy::where('school_id', $school->id)
            ->where('status', 'available')
            ->limit(3)
            ->get();

        if ($availableCopies->isEmpty()) {
            return;
        }

        $loans = [
            [
                'borrower_id' => '1',
                'borrower_type' => 'student',
                'days_ago_borrowed' => 10,
                'status' => 'active',
            ],
            [
                'borrower_id' => '1',
                'borrower_type' => 'student',
                'days_ago_borrowed' => 20,
                'status' => 'overdue',
            ],
            [
                'borrower_id' => '1',
                'borrower_type' => 'student',
                'days_ago_borrowed' => 5,
                'status' => 'active',
            ],
        ];

        foreach ($availableCopies as $index => $copy) {
            if (! isset($loans[$index])) {
                break;
            }

            $loanData = $loans[$index];
            $loanDate = now()->subDays($loanData['days_ago_borrowed']);
            $dueDate = (clone $loanDate)->addDays(14);

            BookLoan::firstOrCreate(
                [
                    'school_id' => $school->id,
                    'book_copy_id' => $copy->id,
                    'borrower_id' => $loanData['borrower_id'],
                ],
                [
                    'borrower_type' => $loanData['borrower_type'],
                    'loan_date' => $loanDate,
                    'due_date' => $dueDate,
                    'return_date' => null,
                    'status' => $loanData['status'],
                    'renewal_count' => 0,
                    'issued_by' => null,
                    'returned_to' => null,
                    'notes' => null,
                ]
            );

            $copy->update([
                'is_available' => false,
                'status' => 'borrowed',
            ]);
        }
    }
}
