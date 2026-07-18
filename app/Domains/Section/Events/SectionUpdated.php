<?php

declare(strict_types=1);

namespace App\Domains\Section\Events;

use App\Models\Section;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SectionUpdated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly Section $section,
    ) {
    }
}
