<?php

namespace App\Domains\Guardian\Listeners;

use App\Domains\Guardian\Events\GuardianUpdated;
use Illuminate\Support\Facades\Log;

class LogGuardianUpdated
{
    public function handle(GuardianUpdated $event): void
    {
        Log::info('Guardian updated', [
            'guardian_id' => $event->guardian->id,
            'name' => $event->guardian->full_name,
            'status' => $event->guardian->status->value,
        ]);
    }
}
