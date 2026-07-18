<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domains\System\Services\BackupService;
use App\Jobs\System\ProcessBackup;
use Illuminate\Console\Command;

class SystemBackupCommand extends Command
{
    protected $signature = 'system:backup {--name= : Backup name} {--type=manual : Backup type (manual|scheduled)}';

    protected $description = 'Create a system backup';

    public function handle(BackupService $backupService): int
    {
        $name = $this->option('name') ?: 'Backup ' . now()->format('Y-m-d_His');
        $type = $this->option('type');

        $backup = $backupService->create([
            'name' => $name,
            'type' => $type,
        ]);

        ProcessBackup::dispatch($backup->id);

        $this->info("Backup created: {$backup->name} ({$backup->id})");

        return self::SUCCESS;
    }
}
