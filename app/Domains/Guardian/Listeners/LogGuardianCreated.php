<?php

namespace App\Domains\Guardian\Listeners;

use App\Domains\Guardian\Events\GuardianCreated;
use Illuminate\Support\Facades\Log;

class LogGuardianCreated
{
    public function handle(GuardianCreated $event): void
    {
        Log::info('Guardian created', [
            'guardian_id' => $event->guardian->id,
            'guardian_number' => $event->guardian->guardian_number,
            'name' => $event->guardian->full_name,
            'school_id' => $event->guardian->school_id,
        ]);
    }
}
