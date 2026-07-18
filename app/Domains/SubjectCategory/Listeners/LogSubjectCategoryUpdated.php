<?php

declare(strict_types=1);

namespace App\Domains\SubjectCategory\Listeners;

use App\Domains\SubjectCategory\Events\SubjectCategoryUpdated;
use Illuminate\Support\Facades\Log;

class LogSubjectCategoryUpdated
{
    public function handle(SubjectCategoryUpdated $event): void
    {
        Log::info('Subject category updated', [
            'subject_category_id' => $event->subjectCategory->id,
            'name' => $event->subjectCategory->name,
            'status' => $event->subjectCategory->status->value,
        ]);
    }
}
