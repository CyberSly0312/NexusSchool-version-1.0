<?php

namespace App\Domains\GradeLevel\Listeners;

use App\Domains\GradeLevel\Events\GradeLevelArchived;
use Illuminate\Support\Facades\Log;

class LogGradeLevelArchived
{
    public function handle(GradeLevelArchived $event): void
    {
        Log::info('Grade level archived', [
            'grade_level_id' => $event->gradeLevel->id,
            'name' => $event->gradeLevel->name,
            'program_id' => $event->gradeLevel->program_id,
        ]);
    }
}
