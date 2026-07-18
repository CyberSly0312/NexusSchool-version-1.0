<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\CreateAnalyticsSnapshot;
use Illuminate\Console\Command;

class CreateDailySnapshots extends Command
{
    protected $signature = 'analytics:daily-snapshots';

    protected $description = 'Create daily analytics snapshots for all schools';

    public function handle(): int
    {
        $schoolIds = \DB::table('tenants')->where('status', 'active')->pluck('id');

        if ($schoolIds->isEmpty()) {
            $this->info('No active schools found.');

            return self::SUCCESS;
        }

        $snapshotTypes = ['enrollment', 'attendance', 'library', 'transport', 'communication'];

        foreach ($schoolIds as $schoolId) {
            foreach ($snapshotTypes as $type) {
                CreateAnalyticsSnapshot::dispatch($schoolId, $type);
            }
            $this->info("Dispatched snapshots for school: {$schoolId}");
        }

        $this->info('Dispatched '.($schoolIds->count() * count($snapshotTypes))." snapshot job(s) for {$schoolIds->count()} school(s).");

        return self::SUCCESS;
    }
}
