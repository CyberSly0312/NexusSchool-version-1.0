<?php

declare(strict_types=1);

namespace App\Console\Commands\System;

use App\Domains\System\Services\BackupService;
use Illuminate\Console\Command;

class SystemBackupCommand extends Command
{
    protected $signature = 'system:backup {name? : Nom de la sauvegarde} {--disk=local : Disque de stockage}';

    protected $description = 'Créer une sauvegarde système';

    public function handle(BackupService $backupService): int
    {
        $name = $this->argument('name') ?? 'Sauvegarde ' . now()->format('Y-m-d H:i:s');
        $disk = $this->option('disk');

        $this->info("Création de la sauvegarde : {$name}");

        $backup = $backupService->create([
            'name' => $name,
            'type' => 'manual',
            'disk' => $disk,
        ]);

        $this->info('Sauvegarde créée. Exécution en cours...');

        $result = $backupService->executeBackup($backup);

        if ($result->status === 'completed') {
            $this->info('Sauvegarde terminée avec succès.');
            $this->line("  Chemin : {$result->path}");
            $this->line('  Taille : ' . number_format($result->sizeBytes ?? 0, 0, ',', ' ') . ' octets');
        } else {
            $this->error("Échec de la sauvegarde : {$result->error}");

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
