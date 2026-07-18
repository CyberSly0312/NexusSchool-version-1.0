<?php

declare(strict_types=1);

namespace App\Domains\Ai\Services;

use App\Domains\Ai\DTOs\AiUsageLogData;
use App\Domains\Ai\Events\UsageQuotaExceeded;
use App\Models\AiUsageLog;
use Illuminate\Support\Collection;

class AiService
{
    public function __construct(
        protected ProviderService $providerService,
        protected ConversationService $conversationService,
        protected PromptService $promptService,
        protected KnowledgeService $knowledgeService,
        protected AiUsageLog $usageModel = new AiUsageLog(),
    ) {
    }

    public function recordUsage(array $data): AiUsageLogData
    {
        $data['total_tokens'] = ($data['input_tokens'] ?? 0) + ($data['output_tokens'] ?? 0);
        $data['status'] = $data['status'] ?? 'success';

        $log = $this->usageModel->create($data);

        $this->checkQuotas($data['school_id'] ?? '', $data['ai_provider_id'] ?? null);

        return AiUsageLogData::create($log->toArray());
    }

    public function getUsageStats(string $schoolId, ?string $from = null, ?string $to = null): array
    {
        $query = $this->usageModel->where('school_id', $schoolId);

        if ($from && $to) {
            $query->whereBetween('created_at', [$from, $to]);
        }

        $stats = $query->selectRaw('
            COUNT(*) as total_requests,
            SUM(total_tokens) as total_tokens,
            SUM(input_tokens) as total_input_tokens,
            SUM(output_tokens) as total_output_tokens,
            SUM(estimated_cost) as total_cost,
            AVG(latency_ms) as avg_latency_ms,
            SUM(CASE WHEN was_cached = 1 THEN 1 ELSE 0 END) as cached_requests,
            SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as failed_requests,
            SUM(CASE WHEN is_billable = 1 THEN 1 ELSE 0 END) as billable_requests
        ', ['failed'])->first();

        return [
            'total_requests' => (int) $stats->total_requests,
            'total_tokens' => (int) $stats->total_tokens,
            'total_input_tokens' => (int) $stats->total_input_tokens,
            'total_output_tokens' => (int) $stats->total_output_tokens,
            'total_cost' => (float) $stats->total_cost,
            'avg_latency_ms' => (float) $stats->avg_latency_ms,
            'cached_requests' => (int) $stats->cached_requests,
            'failed_requests' => (int) $stats->failed_requests,
            'billable_requests' => (int) $stats->billable_requests,
            'cache_hit_rate' => $stats->total_requests > 0
                ? round(($stats->cached_requests / $stats->total_requests) * 100, 2)
                : 0,
        ];
    }

    public function getProviderUsage(string $schoolId, string $providerId, ?string $from = null, ?string $to = null): array
    {
        $query = $this->usageModel
            ->where('school_id', $schoolId)
            ->where('ai_provider_id', $providerId);

        if ($from && $to) {
            $query->whereBetween('created_at', [$from, $to]);
        }

        $stats = $query->selectRaw('
            COUNT(*) as total_requests,
            SUM(total_tokens) as total_tokens,
            SUM(estimated_cost) as total_cost,
            AVG(latency_ms) as avg_latency_ms
        ')->first();

        return [
            'total_requests' => (int) $stats->total_requests,
            'total_tokens' => (int) $stats->total_tokens,
            'total_cost' => (float) $stats->total_cost,
            'avg_latency_ms' => (float) $stats->avg_latency_ms,
        ];
    }

    public function getUsageLogs(string $schoolId, array $filters = []): Collection
    {
        $query = $this->usageModel
            ->with(['provider', 'conversation', 'user'])
            ->where('school_id', $schoolId);

        if (isset($filters['provider_id'])) {
            $query->where('ai_provider_id', $filters['provider_id']);
        }

        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (isset($filters['action'])) {
            $query->where('action', $filters['action']);
        }

        if (isset($filters['from']) && isset($filters['to'])) {
            $query->whereBetween('created_at', [$filters['from'], $filters['to']]);
        }

        return $query->latest()->get();
    }

    public function dailyUsage(string $schoolId): int
    {
        return (int) $this->usageModel
            ->where('school_id', $schoolId)
            ->whereDate('created_at', today())
            ->sum('total_tokens');
    }

    public function monthlyUsage(string $schoolId): int
    {
        return (int) $this->usageModel
            ->where('school_id', $schoolId)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_tokens');
    }

    public function checkQuotas(string $schoolId, ?string $providerId): void
    {
        $dailyLimit = config('ai.quotas.daily_tokens', 1000000);
        $monthlyLimit = config('ai.quotas.monthly_tokens', 30000000);

        $dailyUsage = $this->dailyUsage($schoolId);
        if ($dailyUsage >= $dailyLimit) {
            UsageQuotaExceeded::dispatch($schoolId, 'daily', $dailyUsage, $dailyLimit);
        }

        $monthlyUsage = $this->monthlyUsage($schoolId);
        if ($monthlyUsage >= $monthlyLimit) {
            UsageQuotaExceeded::dispatch($schoolId, 'monthly', $monthlyUsage, $monthlyLimit);
        }

        if ($providerId) {
            $provider = $this->providerService->findById($providerId);

            if ($provider->daily_token_limit) {
                $providerDaily = (int) $this->usageModel
                    ->where('school_id', $schoolId)
                    ->where('ai_provider_id', $providerId)
                    ->whereDate('created_at', today())
                    ->sum('total_tokens');

                if ($providerDaily >= $provider->daily_token_limit) {
                    UsageQuotaExceeded::dispatch($schoolId, 'provider_daily', $providerDaily, $provider->daily_token_limit);
                }
            }
        }
    }

    public function getProviders(): ProviderService
    {
        return $this->providerService;
    }

    public function getConversations(): ConversationService
    {
        return $this->conversationService;
    }

    public function getPrompts(): PromptService
    {
        return $this->promptService;
    }

    public function getKnowledge(): KnowledgeService
    {
        return $this->knowledgeService;
    }
}
