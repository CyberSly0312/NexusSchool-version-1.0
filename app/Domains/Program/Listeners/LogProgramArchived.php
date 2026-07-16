<?php

namespace App\Domains\Program\Listeners;

use App\Domains\Program\Events\ProgramArchived;
use Illuminate\Support\Facades\Log;

class LogProgramArchived
{
    public function handle(ProgramArchived $event): void
    {
        Log::info('Program archived', [
            'program_id' => $event->program->id,
            'name' => $event->program->name,
            'school_id' => $event->program->school_id,
        ]);
    }
}
