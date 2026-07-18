<?php

declare(strict_types=1);

namespace App\Domains\Library\Listeners;

use App\Domains\Library\Events\FineGenerated;
use Illuminate\Support\Facades\Log;

class LogFineGenerated
{
    public function handle(FineGenerated $event): void
    {
        Log::info('Fine generated', [
            'fine_id' => $event->fine->id,
            'borrower_id' => $event->fine->borrower_id,
            'borrower_type' => $event->fine->borrower_type->value,
            'amount' => $event->fine->amount,
            'reason' => $event->fine->reason,
            'school_id' => $event->fine->school_id,
        ]);
    }
}
