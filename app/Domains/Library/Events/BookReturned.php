<?php

declare(strict_types=1);

namespace App\Domains\Library\Events;

use App\Models\BookLoan;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookReturned
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly BookLoan $loan,
    ) {
    }
}
