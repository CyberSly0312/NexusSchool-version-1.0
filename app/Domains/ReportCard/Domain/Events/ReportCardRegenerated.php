<?php

declare(strict_types=1);

namespace App\Domains\ReportCard\Domain\Events;

use App\Domains\ReportCard\Infrastructure\Models\ReportCard;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReportCardRegenerated
{
    use Dispatchable;
    use SerializesModels;

    /**
     * @param  array<int, string>  $documentNumbers
     */
    public function __construct(
        public readonly ReportCard $reportCard,
        public readonly array $documentNumbers = [],
        public readonly ?User $actor = null,
    ) {
    }
}
