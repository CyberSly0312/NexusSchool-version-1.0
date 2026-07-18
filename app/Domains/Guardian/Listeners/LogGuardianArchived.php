<?php

declare(strict_types=1);

namespace App\Domains\Guardian\Listeners;

use App\Domains\Guardian\Events\GuardianArchived;
use Illuminate\Support\Facades\Log;

class LogGuardianArchived
{
    public function handle(GuardianArchived $event): void
    {
        Log::info('Guardian archived', [
            'guardian_id' => $event->guardian->id,
            'guardian_number' => $event->guardian->guardian_number,
            'school_id' => $event->guardian->school_id,
        ]);
    }
}
