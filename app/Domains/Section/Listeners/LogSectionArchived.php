<?php

declare(strict_types=1);

namespace App\Domains\Section\Listeners;

use App\Domains\Section\Events\SectionArchived;
use Illuminate\Support\Facades\Log;

class LogSectionArchived
{
    public function handle(SectionArchived $event): void
    {
        Log::info('Section archived', [
            'section_id' => $event->section->id,
            'name' => $event->section->name,
            'grade_level_id' => $event->section->grade_level_id,
        ]);
    }
}
