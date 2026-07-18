<?php

namespace Database\Seeders;

use App\Domains\School\Models\School;
use App\Models\Fine;
use Illuminate\Database\Seeder;

class FineSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::where('slug', 'nexus-demo')->first();
        if (! $school) {
            return;
        }

        Fine::firstOrCreate(
            [
                'school_id' => $school->id,
                'borrower_id' => '1',
                'reason' => 'Overdue book',
            ],
            [
                'book_loan_id' => null,
                'borrower_type' => 'student',
                'amount' => 5.00,
                'paid_amount' => 0,
                'waived_amount' => 0,
                'fine_date' => now(),
                'paid_date' => null,
                'waived_date' => null,
                'payment_method' => null,
                'reference_number' => null,
                'status' => 'pending',
                'processed_by' => null,
                'notes' => 'Fine for returning The Gold Coast Memoirs 5 days late.',
            ]
        );
    }
}
