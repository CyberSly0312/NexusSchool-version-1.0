<?php

namespace Database\Seeders;

use App\Domains\School\Models\School;
use App\Models\Book;
use App\Models\BookCopy;
use Illuminate\Database\Seeder;

class BookCopySeeder extends Seeder
{
    public function run(): void
    {
        $school = School::where('slug', 'nexus-demo')->first();
        if (! $school) {
            return;
        }

        $books = Book::where('school_id', $school->id)->get();

        foreach ($books as $book) {
            $copyCount = rand(2, 3);

            for ($i = 1; $i <= $copyCount; $i++) {
                BookCopy::firstOrCreate(
                    [
                        'school_id' => $school->id,
                        'book_id' => $book->id,
                        'barcode' => 'BC-'.str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT),
                    ],
                    [
                        'inventory_number' => 'INV-'.str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT),
                        'acquisition_date' => now()->subMonths(rand(1, 12)),
                        'purchase_cost' => round(rand(1000, 15000) / 100, 2),
                        'condition' => 'good',
                        'is_available' => true,
                        'status' => 'available',
                        'notes' => null,
                    ]
                );
            }
        }
    }
}
