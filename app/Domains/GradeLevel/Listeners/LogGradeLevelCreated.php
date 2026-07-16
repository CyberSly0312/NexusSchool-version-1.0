<?php

namespace App\Domains\GradeLevel\Listeners;

use App\Domains\GradeLevel\Events\GradeLevelCreated;
use Illuminate\Support\Facades\Log;

class LogGradeLevelCreated
{
    public function handle(GradeLevelCreated $event): void
    {
        Log::info('Grade level created', [
            'grade_level_id' => $event->gradeLevel->id,
            'name' => $event->gradeLevel->name,
            'code' => $event->gradeLevel->code,
            'program_id' => $event->gradeLevel->program_id,
        ]);
    }
}
