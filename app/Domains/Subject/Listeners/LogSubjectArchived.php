<?php

namespace App\Domains\Subject\Listeners;

use App\Domains\Subject\Events\SubjectArchived;
use Illuminate\Support\Facades\Log;

class LogSubjectArchived
{
    public function handle(SubjectArchived $event): void
    {
        Log::info('Subject archived', [
            'subject_id' => $event->subject->id,
            'name' => $event->subject->name,
            'school_id' => $event->subject->school_id,
        ]);
    }
}
