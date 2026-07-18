<?php

declare(strict_types=1);

namespace App\Domains\Library\Listeners;

use App\Domains\Library\Events\BookReturned;
use Illuminate\Support\Facades\Log;

class LogBookReturned
{
    public function handle(BookReturned $event): void
    {
        Log::info('Book returned', [
            'loan_id' => $event->loan->id,
            'book_copy_id' => $event->loan->book_copy_id,
            'borrower_id' => $event->loan->borrower_id,
            'return_date' => $event->loan->return_date->format('Y-m-d'),
            'school_id' => $event->loan->school_id,
        ]);
    }
}
