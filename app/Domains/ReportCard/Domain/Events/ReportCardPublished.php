<?php

declare(strict_types=1);

namespace App\Domains\ReportCard\Domain\Events;

use App\Domains\ReportCard\Infrastructure\Models\ReportCard;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReportCardPublished
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly ReportCard $reportCard,
        public readonly int $publishedEntries = 0,
        public readonly ?User $actor = null,
    ) {
    }
}
