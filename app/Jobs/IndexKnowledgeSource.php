<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Domains\Ai\Enums\AiKnowledgeSourceStatus;
use App\Domains\Ai\Events\KnowledgeIndexed;
use App\Models\AiKnowledgeSource;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class IndexKnowledgeSource implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public int $timeout = 300;

    public function __construct(
        public string $knowledgeSourceId,
    ) {
    }

    public function handle(): void
    {
        $source = AiKnowledgeSource::findOrFail($this->knowledgeSourceId);

        $source->update(['status' => AiKnowledgeSourceStatus::Indexing->value]);

        $chunkCount = 0;
        $totalTokens = 0;

        match ($source->type) {
            'markdown' => $this->indexMarkdown($source, $chunkCount, $totalTokens),
            'text' => $this->indexText($source, $chunkCount, $totalTokens),
            'database' => $this->indexDatabase($source, $chunkCount, $totalTokens),
            'pdf_metadata' => $this->indexPdfMetadata($source, $chunkCount, $totalTokens),
            default => null,
        };

        $source->update([
            'status' => AiKnowledgeSourceStatus::Indexed->value,
            'chunk_count' => $chunkCount,
            'total_tokens' => $totalTokens,
            'indexed_at' => now(),
            'version' => $source->version + 1,
        ]);

        KnowledgeIndexed::dispatch($source->fresh());
    }

    private function indexMarkdown(AiKnowledgeSource $source, int &$chunkCount, int &$totalTokens): void
    {
        $chunkSize = config('ai.knowledge.chunk_size', 1000);
        $chunkOverlap = config('ai.knowledge.chunk_overlap', 200);

        $content = $source->source_path ? @file_get_contents($source->source_path) : '';
        if (! $content) {
            $chunkCount = 0;
            $totalTokens = 0;

            return;
        }

        $sentences = preg_split('/(?<=[.!?])\s+/', $content);
        $currentChunk = '';
        $tokenEstimate = 0;

        foreach ($sentences as $sentence) {
            $currentChunk .= $sentence . ' ';
            $tokenEstimate += (int) ceil(str_word_count($sentence) * 1.3);

            if ($tokenEstimate >= $chunkSize) {
                $chunkCount++;
                $totalTokens += $tokenEstimate;
                $currentChunk = '';
                $tokenEstimate = 0;
            }
        }

        if (trim($currentChunk) !== '') {
            $chunkCount++;
            $totalTokens += $tokenEstimate;
        }
    }

    private function indexText(AiKnowledgeSource $source, int &$chunkCount, int &$totalTokens): void
    {
        $this->indexMarkdown($source, $chunkCount, $totalTokens);
    }

    private function indexDatabase(AiKnowledgeSource $source, int &$chunkCount, int &$totalTokens): void
    {
        $config = $source->configuration ?? [];
        $tableName = $config['table'] ?? null;

        if (! $tableName) {
            return;
        }

        $count = \DB::table($tableName)->count();
        $chunkCount = (int) ceil($count / 100);
        $totalTokens = $count * 50;
    }

    private function indexPdfMetadata(AiKnowledgeSource $source, int &$chunkCount, int &$totalTokens): void
    {
        $chunkCount = 1;
        $totalTokens = 100;
    }

    public function failed(\Throwable $exception): void
    {
        $source = AiKnowledgeSource::find($this->knowledgeSourceId);

        if ($source) {
            $source->update([
                'status' => AiKnowledgeSourceStatus::Failed->value,
                'metadata' => array_merge($source->metadata ?? [], ['last_error' => $exception->getMessage()]),
            ]);
        }
    }
}
