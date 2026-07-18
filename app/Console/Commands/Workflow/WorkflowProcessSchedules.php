<?php

declare(strict_types=1);

namespace App\Console\Commands\Workflow;

use App\Domains\Workflow\Services\ScheduleService;
use Illuminate\Console\Command;

class WorkflowProcessSchedules extends Command
{
    protected $signature = 'workflow:process-schedules';

    protected $description = 'Process all due workflow schedules';

    public function handle(ScheduleService $scheduleService): int
    {
        $schedules = $scheduleService->getDueSchedules();

        $this->info("Found {$schedules->count()} due schedule(s).");

        foreach ($schedules as $schedule) {
            try {
                $scheduleService->processSchedule($schedule);
                $this->info("Processed schedule: {$schedule->name}");
            } catch (\Throwable $e) {
                $this->error("Failed to process schedule {$schedule->name}: {$e->getMessage()}");
            }
        }

        return self::SUCCESS;
    }
}
