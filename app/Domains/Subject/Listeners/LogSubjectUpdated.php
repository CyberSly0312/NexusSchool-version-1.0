<?php

declare(strict_types=1);

namespace App\Domains\Subject\Listeners;

use App\Domains\Subject\Events\SubjectUpdated;
use Illuminate\Support\Facades\Log;

class LogSubjectUpdated
{
    public function handle(SubjectUpdated $event): void
    {
        Log::info('Subject updated', [
            'subject_id' => $event->subject->id,
            'name' => $event->subject->name,
            'status' => $event->subject->status->value,
        ]);
    }
}
