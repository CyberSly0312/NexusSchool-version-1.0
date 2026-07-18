<?php

declare(strict_types=1);

namespace App\Domains\Subject\Listeners;

use App\Domains\Subject\Events\SubjectCreated;
use Illuminate\Support\Facades\Log;

class LogSubjectCreated
{
    public function handle(SubjectCreated $event): void
    {
        Log::info('Subject created', [
            'subject_id' => $event->subject->id,
            'name' => $event->subject->name,
            'code' => $event->subject->code,
            'school_id' => $event->subject->school_id,
        ]);
    }
}
