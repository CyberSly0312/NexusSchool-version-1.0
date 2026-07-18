<?php

declare(strict_types=1);

namespace App\Domains\Library\Listeners;

use App\Domains\Library\Events\BookCreated;
use Illuminate\Support\Facades\Log;

class LogBookCreated
{
    public function handle(BookCreated $event): void
    {
        Log::info('Book created', [
            'book_id' => $event->book->id,
            'title' => $event->book->title,
            'isbn' => $event->book->isbn,
            'school_id' => $event->book->school_id,
        ]);
    }
}
