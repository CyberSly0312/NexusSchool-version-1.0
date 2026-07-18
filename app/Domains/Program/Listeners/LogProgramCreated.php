<?php

declare(strict_types=1);

namespace App\Domains\Program\Listeners;

use App\Domains\Program\Events\ProgramCreated;
use Illuminate\Support\Facades\Log;

class LogProgramCreated
{
    public function handle(ProgramCreated $event): void
    {
        Log::info('Program created', [
            'program_id' => $event->program->id,
            'name' => $event->program->name,
            'code' => $event->program->code,
            'school_id' => $event->program->school_id,
        ]);
    }
}
