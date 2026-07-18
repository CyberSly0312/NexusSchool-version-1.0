<?php

declare(strict_types=1);

namespace App\Console\Commands\System;

use App\Domains\System\Services\BackupService;
use Illuminate\Console\Command;

class SystemBackupCommand extends Command
{
    protected $signature = 'system:backup {name? : Backup name} {--disk=local : Storage disk}';

    protected $description = 'Create a system backup';

    public function handle(BackupService $backupService): int
    {
        $name = $this->argument('name') ?? 'Backup ' . now()->format('Y-m-d H:i:s');
        $disk = $this->option('disk');

        $this->info("Creating backup: {$name}");

        $backup = $backupService->create([
            'name' => $name,
            'type' => 'manual',
            'disk' => $disk,
        ]);

        $this->info('Backup created. Executing...');

        $result = $backupService->executeBackup($backup);

        if ($result->status === 'completed') {
            $this->info('Backup completed successfully.');
            $this->line("  Path: {$result->path}");
            $this->line('  Size: ' . number_format($result->sizeBytes ?? 0) . ' bytes');
        } else {
            $this->error("Backup failed: {$result->error}");

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
