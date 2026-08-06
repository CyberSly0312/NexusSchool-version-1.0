<?php

declare(strict_types=1);

namespace App\Domains\ReportCard\Domain\Events;

use App\Domains\ReportCard\Infrastructure\Models\ReportCard;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReportCardArchived
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly ReportCard $reportCard,
        public readonly ?User $actor = null,
    ) {
    }
}
