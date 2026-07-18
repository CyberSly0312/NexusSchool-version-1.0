<?php

declare(strict_types=1);

namespace App\Domains\Library\Listeners;

use App\Domains\Library\Events\ReservationCreated;
use Illuminate\Support\Facades\Log;

class LogReservationCreated
{
    public function handle(ReservationCreated $event): void
    {
        Log::info('Reservation created', [
            'reservation_id' => $event->reservation->id,
            'book_id' => $event->reservation->book_id,
            'borrower_id' => $event->reservation->borrower_id,
            'borrower_type' => $event->reservation->borrower_type->value,
            'expiry_date' => $event->reservation->expiry_date->format('Y-m-d'),
            'school_id' => $event->reservation->school_id,
        ]);
    }
}
