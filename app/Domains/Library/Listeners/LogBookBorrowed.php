<?php

declare(strict_types=1);

namespace App\Domains\Library\Listeners;

use App\Domains\Library\Events\BookBorrowed;
use Illuminate\Support\Facades\Log;

class LogBookBorrowed
{
    public function handle(BookBorrowed $event): void
    {
        Log::info('Book borrowed', [
            'loan_id' => $event->loan->id,
            'book_copy_id' => $event->loan->book_copy_id,
            'borrower_id' => $event->loan->borrower_id,
            'borrower_type' => $event->loan->borrower_type->value,
            'due_date' => $event->loan->due_date->format('Y-m-d'),
            'school_id' => $event->loan->school_id,
        ]);
    }
}
