<?php

namespace App\Domains\SubjectCategory\Listeners;

use App\Domains\SubjectCategory\Events\SubjectCategoryArchived;
use Illuminate\Support\Facades\Log;

class LogSubjectCategoryArchived
{
    public function handle(SubjectCategoryArchived $event): void
    {
        Log::info('Subject category archived', [
            'subject_category_id' => $event->subjectCategory->id,
            'name' => $event->subjectCategory->name,
            'school_id' => $event->subjectCategory->school_id,
        ]);
    }
}
