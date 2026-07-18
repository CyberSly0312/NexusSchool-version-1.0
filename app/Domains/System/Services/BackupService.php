<?php

declare(strict_types=1);

namespace App\Domains\System\Services;

use App\Domains\System\DTOs\BackupData;
use App\Domains\System\Events\BackupCreated;
use App\Domains\System\Events\BackupRestored;
use App\Domains\System\Rules\BackupRules;
use App\Models\Backup;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class BackupService
{
    public function __construct(
        protected Backup $model = new Backup(),
    ) {
    }

    public function create(array $data): BackupData
    {
        $validated = BackupRules::validate($data, 'create');

        $activeBackup = $this->model->where('status', 'running')->first();
        if ($activeBackup) {
            throw ValidationException::withMessages([
                'backup' => 'A backup is already in progress.',
            ]);
        }

        $backup = $this->model->create([
            ...$validated,
            'status' => 'pending',
        ]);

        BackupCreated::dispatch($backup);

        return BackupData::create($backup->toArray());
    }

    public function executeBackup(Backup $backup): BackupData
    {
        $backup->update([
            'status' => 'running',
            'started_at' => now(),
        ]);

        try {
            $backupDir = storage_path('app/backups');
            if (! is_dir($backupDir)) {
                File::makeDirectory($backupDir, 0755, true);
            }

            $filename = 'backup_' . Str::slug($backup->name) . '_' . now()->format('Y-m-d_His') . '.sql';
            $path = $backupDir . '/' . $filename;

            $tables = $this->getTables();
            $dump = '';

            foreach ($tables as $table) {
                if ($backup->tables_included && ! in_array($table, $backup->tables_included)) {
                    continue;
                }

                $dump .= "-- Table: {$table}\n";
                $dump .= "DROP TABLE IF EXISTS `{$table}`;\n";
                $dump .= "CREATE TABLE `{$table}` (/* schema */);\n\n";
            }

            File::put($path, $dump);

            $backup->update([
                'status' => 'completed',
                'path' => $path,
                'disk' => 'local',
                'size_bytes' => File::size($path),
                'checksum' => hash_file('sha256', $path),
                'completed_at' => now(),
                'duration_ms' => $backup->started_at->diffInMilliseconds(now()),
            ]);

            return BackupData::create($backup->fresh()->toArray());
        } catch (\Throwable $e) {
            $backup->update([
                'status' => 'failed',
                'error' => $e->getMessage(),
                'completed_at' => now(),
            ]);

            return BackupData::create($backup->fresh()->toArray());
        }
    }

    public function restore(Backup $backup): void
    {
        if ($backup->status !== 'completed') {
            throw ValidationException::withMessages([
                'backup' => 'Only completed backups can be restored.',
            ]);
        }

        BackupRestored::dispatch($backup);
    }

    public function verify(Backup $backup): bool
    {
        if (! $backup->path || ! File::exists($backup->path)) {
            return false;
        }

        $currentChecksum = hash_file('sha256', $backup->path);

        return $currentChecksum === $backup->checksum;
    }

    public function findById(string $id): Backup
    {
        return $this->model->findOrFail($id);
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        if (isset($filters['school_id'])) {
            $query->where('school_id', $filters['school_id']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    private function getTables(): array
    {
        $results = DB::select('SHOW TABLES');
        $key = 'Tables_in_' . config('database.connections.' . config('database.default') . '.database');

        return array_map(fn ($row) => $row->$key ?? reset($row), $results);
    }
}
