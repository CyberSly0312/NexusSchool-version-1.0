<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\AiUsageLog;
use Illuminate\Console\Command;

class AiUsageReport extends Command
{
    protected $signature = 'ai:usage-report {--school= : ID de l\'école} {--period=day : Période du rapport (day|week|month)}';

    protected $description = 'Générer le rapport d\'utilisation IA pour une école';

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

        $this->info('Rapport d\'utilisation IA');
        $this->info('========================');
        $this->info("Période : {$period}");
        $this->info("Requêtes totales : {$stats->total_requests}");
        $this->info("Jetons totaux : {$stats->total_tokens}");
        $this->info('Coût total : ' . number_format((float) $stats->total_cost, 0, ',', ' ') . ' FCFA');
        $this->info('Latence moyenne : ' . number_format((float) $stats->avg_latency, 0) . ' ms');
        $this->info("Requêtes échouées : {$stats->failed}");

        return Command::SUCCESS;
    }
}
