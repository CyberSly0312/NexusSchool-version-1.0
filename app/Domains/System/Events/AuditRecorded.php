<?php

declare(strict_types=1);

namespace App\Domains\System\Events;

use App\Models\AuditLog;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AuditRecorded
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly AuditLog $auditLog,
    ) {
    }
}
