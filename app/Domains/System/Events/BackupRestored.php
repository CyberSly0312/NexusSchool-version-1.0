<?php

declare(strict_types=1);

namespace App\Domains\System\Events;

use App\Models\Backup;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BackupRestored
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly Backup $backup,
    ) {
    }
}
