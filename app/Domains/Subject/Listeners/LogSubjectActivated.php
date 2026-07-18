<?php

declare(strict_types=1);

namespace App\Domains\Subject\Listeners;

use App\Domains\Subject\Events\SubjectActivated;
use Illuminate\Support\Facades\Log;

class LogSubjectActivated
{
    public function handle(SubjectActivated $event): void
    {
        Log::info('Subject activated', [
            'subject_id' => $event->subject->id,
            'name' => $event->subject->name,
            'school_id' => $event->subject->school_id,
        ]);
    }
}
