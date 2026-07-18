<?php

declare(strict_types=1);

namespace App\Domains\Library\Listeners;

use App\Domains\Library\Events\ReservationExpired;
use Illuminate\Support\Facades\Log;

class LogReservationExpired
{
    public function handle(ReservationExpired $event): void
    {
        Log::info('Reservation expired', [
            'reservation_id' => $event->reservation->id,
            'book_id' => $event->reservation->book_id,
            'borrower_id' => $event->reservation->borrower_id,
            'borrower_type' => $event->reservation->borrower_type->value,
            'school_id' => $event->reservation->school_id,
        ]);
    }
}
