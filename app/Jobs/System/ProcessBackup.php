<?php

declare(strict_types=1);

namespace App\Jobs\System;

use App\Domains\System\Services\BackupService;
use App\Models\Backup;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessBackup implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 1;

    public int $timeout = 600;

    public function __construct(
        public string $backupId,
    ) {
    }

    public function handle(BackupService $backupService): void
    {
        $backup = Backup::findOrFail($this->backupId);

        $backupService->executeBackup($backup);
    }

    public function failed(\Throwable $exception): void
    {
        \Illuminate\Support\Facades\Log::error('Backup Processing Failed', [
            'backup_id' => $this->backupId,
            'error' => $exception->getMessage(),
        ]);
    }
}
