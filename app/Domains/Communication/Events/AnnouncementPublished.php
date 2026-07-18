<?php

declare(strict_types=1);

namespace App\Domains\Communication\Events;

use App\Models\Announcement;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AnnouncementPublished
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly Announcement $announcement,
    ) {
    }
}
