<?php

namespace App\Domains\Program\Listeners;

use App\Domains\Program\Events\ProgramUpdated;
use Illuminate\Support\Facades\Log;

class LogProgramUpdated
{
    public function handle(ProgramUpdated $event): void
    {
        Log::info('Program updated', [
            'program_id' => $event->program->id,
            'name' => $event->program->name,
            'status' => $event->program->status->value,
        ]);
    }
}
