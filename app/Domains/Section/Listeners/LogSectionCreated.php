<?php

declare(strict_types=1);

namespace App\Domains\Section\Listeners;

use App\Domains\Section\Events\SectionCreated;
use Illuminate\Support\Facades\Log;

class LogSectionCreated
{
    public function handle(SectionCreated $event): void
    {
        Log::info('Section created', [
            'section_id' => $event->section->id,
            'name' => $event->section->name,
            'code' => $event->section->code,
            'grade_level_id' => $event->section->grade_level_id,
        ]);
    }
}
