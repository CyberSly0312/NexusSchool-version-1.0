<?php

namespace App\Domains\Guardian\Listeners;

use App\Domains\Guardian\Events\GuardianActivated;
use Illuminate\Support\Facades\Log;

class LogGuardianActivated
{
    public function handle(GuardianActivated $event): void
    {
        Log::info('Guardian activated', [
            'guardian_id' => $event->guardian->id,
            'guardian_number' => $event->guardian->guardian_number,
            'school_id' => $event->guardian->school_id,
        ]);
    }
}
