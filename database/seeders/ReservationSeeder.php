<?php

namespace Database\Seeders;

use App\Domains\School\Models\School;
use App\Models\Book;
use App\Models\Reservation;
use Illuminate\Database\Seeder;

class ReservationSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::where('slug', 'nexus-demo')->first();
        if (! $school) {
            return;
        }

        $books = Book::where('school_id', $school->id)->limit(2)->get();

        foreach ($books as $book) {
            Reservation::firstOrCreate(
                [
                    'school_id' => $school->id,
                    'book_id' => $book->id,
                    'borrower_id' => '1',
                ],
                [
                    'borrower_type' => 'student',
                    'reservation_date' => now(),
                    'expiry_date' => now()->addDays(7),
                    'fulfilled_date' => null,
                    'status' => 'pending',
                    'notes' => null,
                ]
            );
        }
    }
}
