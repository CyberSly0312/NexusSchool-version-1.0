<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\AiUsageLog;
use Illuminate\Console\Command;

class AiUsageReport extends Command
{
    protected $signature = 'ai:usage-report {--school= : School ID} {--period=day : Report period (day|week|month)}';

    protected $description = 'Generate AI usage report for a school';

    public function handle(): int
    {
        $schoolId = $this->option('school');
        $period = $this->option('period');

        $query = AiUsageLog::query();

        if ($schoolId) {
            $query->where('school_id', $schoolId);
        }

        $query->where('created_at', '>=', match ($period) {
            'week' => now()->subWeek(),
            'month' => now()->subMonth(),
            default => now()->subDay(),
        });

        $stats = $query->selectRaw('
            COUNT(*) as total_requests,
            SUM(total_tokens) as total_tokens,
            SUM(estimated_cost) as total_cost,
            AVG(latency_ms) as avg_latency,
            SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as failed
        ', ['failed'])->first();

        $this->info('AI Usage Report');
        $this->info('===============');
        $this->info("Period: {$period}");
        $this->info("Total Requests: {$stats->total_requests}");
        $this->info("Total Tokens: {$stats->total_tokens}");
        $this->info('Total Cost: $' . number_format((float) $stats->total_cost, 4));
        $this->info('Average Latency: ' . number_format((float) $stats->avg_latency, 0) . 'ms');
        $this->info("Failed Requests: {$stats->failed}");

        return Command::SUCCESS;
    }
}
