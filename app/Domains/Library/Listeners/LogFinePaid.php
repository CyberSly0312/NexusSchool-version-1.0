<?php

declare(strict_types=1);

namespace App\Domains\Library\Listeners;

use App\Domains\Library\Events\FinePaid;
use Illuminate\Support\Facades\Log;

class LogFinePaid
{
    public function handle(FinePaid $event): void
    {
        Log::info('Fine paid', [
            'fine_id' => $event->fine->id,
            'borrower_id' => $event->fine->borrower_id,
            'borrower_type' => $event->fine->borrower_type->value,
            'paid_amount' => $event->fine->paid_amount,
            'status' => $event->fine->status->value,
            'school_id' => $event->fine->school_id,
        ]);
    }
}
