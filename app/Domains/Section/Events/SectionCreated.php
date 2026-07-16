<?php

namespace App\Domains\Section\Events;

use App\Models\Section;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SectionCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Section $section,
    ) {}
}
