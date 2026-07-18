<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\AiConversation;
use Illuminate\Console\Command;

class PruneExpiredConversations extends Command
{
    protected $signature = 'ai:prune-conversations {--days=90 : Number of days to keep}';

    protected $description = 'Prune expired AI conversations and their messages';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $cutoffDate = now()->subDays($days);

        $conversations = AiConversation::where('status', '!=', 'active')
            ->where('updated_at', '<', $cutoffDate)
            ->get();

        $count = 0;

        foreach ($conversations as $conversation) {
            $conversation->messages()->delete();
            $conversation->delete();
            $count++;
        }

        $this->info("Pruned {$count} expired conversations (older than {$days} days).");

        return Command::SUCCESS;
    }
}
