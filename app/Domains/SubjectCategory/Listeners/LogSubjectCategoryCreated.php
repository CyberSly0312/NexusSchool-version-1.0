<?php

namespace App\Domains\SubjectCategory\Listeners;

use App\Domains\SubjectCategory\Events\SubjectCategoryCreated;
use Illuminate\Support\Facades\Log;

class LogSubjectCategoryCreated
{
    public function handle(SubjectCategoryCreated $event): void
    {
        Log::info('Subject category created', [
            'subject_category_id' => $event->subjectCategory->id,
            'name' => $event->subjectCategory->name,
            'code' => $event->subjectCategory->code,
            'school_id' => $event->subjectCategory->school_id,
        ]);
    }
}
