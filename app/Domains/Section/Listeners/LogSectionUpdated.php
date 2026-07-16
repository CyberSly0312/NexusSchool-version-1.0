<?php

namespace App\Domains\Section\Listeners;

use App\Domains\Section\Events\SectionUpdated;
use Illuminate\Support\Facades\Log;

class LogSectionUpdated
{
    public function handle(SectionUpdated $event): void
    {
        Log::info('Section updated', [
            'section_id' => $event->section->id,
            'name' => $event->section->name,
            'status' => $event->section->status->value,
        ]);
    }
}
