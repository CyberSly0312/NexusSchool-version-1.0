<?php

declare(strict_types=1);

namespace App\Domains\Library\Events;

use App\Models\Book;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookCreated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly Book $book,
    ) {
    }
}
