<?php
declare(strict_types=1);

namespace App\Domains\Plugin\Services;

use Illuminate\Database\Migrations\Migrator;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Schema;

final class PluginMigrationLoader
{
    private Filesystem $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function runPluginMigrations(string $pluginName): void
    {
        $path = $this->getMigrationPath($pluginName);

        if (!$this->filesystem->isDirectory($path)) {
            return;
        }

        $migrator = app('migrator');
        $files = $migrator->getMigrationFiles($path);

        foreach ($files as $migrationFile => $migrationPath) {
            $this->runSingleMigration($migrationFile, $migrationPath);
        }
    }

    public function rollbackPluginMigrations(string $pluginName): void
    {
        $path = $this->getMigrationPath($pluginName);

        if (!$this->filesystem->isDirectory($path)) {
            return;
        }

        $migrator = app('migrator');
        $files = $migrator->getMigrationFiles($path);

        foreach (array_reverse($files, true) as $migrationFile => $migrationPath) {
            $this->rollbackSingleMigration($migrationFile, $migrationPath);
        }
    }

    private function getMigrationPath(string $pluginName): string
    {
        return config('plugin.install.base_path') . '/' . $pluginName . '/Database/Migrations';
    }

    private function runSingleMigration(string $migrationFile, string $migrationPath): void
    {
        $migration = require $migrationPath;
        if ($migration instanceof \Illuminate\Database\Migrations\Migration) {
            Schema::connection($this->getConnection())->withoutForeignKeyConstraints(function () use ($migration) {
                $migration->up();
            });
        }
    }

    private function rollbackSingleMigration(string $migrationFile, string $migrationPath): void
    {
        $migration = require $migrationPath;
        if ($migration instanceof \Illuminate\Database\Migrations\Migration) {
            $migration->down();
        }
    }

    private function getConnection(): ?string
    {
        return config('database.default');
    }
}
