<?php

declare(strict_types=1);

namespace App\Domains\GradeLevel\Listeners;

use App\Domains\GradeLevel\Events\GradeLevelUpdated;
use Illuminate\Support\Facades\Log;

class LogGradeLevelUpdated
{
    public function handle(GradeLevelUpdated $event): void
    {
        Log::info('Grade level updated', [
            'grade_level_id' => $event->gradeLevel->id,
            'name' => $event->gradeLevel->name,
            'status' => $event->gradeLevel->status->value,
        ]);
    }
}
