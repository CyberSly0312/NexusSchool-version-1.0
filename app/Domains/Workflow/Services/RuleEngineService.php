<?php

declare(strict_types=1);

namespace App\Domains\Workflow\Services;

use App\Domains\Workflow\Enums\NodeType;
use App\Models\WorkflowExecution;
use App\Models\WorkflowVersion;
use Illuminate\Support\Facades\Log;

class RuleEngineService
{
    public function evaluateNode(array $node, WorkflowExecution $execution): array
    {
        $nodeType = $node['type'] ?? null;

        return match ($nodeType) {
            NodeType::Decision->value => $this->evaluateDecision($node, $execution),
            NodeType::Condition->value => $this->evaluateCondition($node, $execution),
            NodeType::Approval->value => $this->evaluateApproval($node, $execution),
            NodeType::Delay->value => $this->evaluateDelay($node, $execution),
            NodeType::Start->value => ['action' => 'proceed', 'next' => $this->findNextNode($node, $execution)],
            NodeType::End->value => ['action' => 'complete'],
            NodeType::Notification->value => $this->evaluateNotification($node, $execution),
            NodeType::ApiCall->value => $this->evaluateApiCall($node, $execution),
            NodeType::AiAction->value => $this->evaluateAiAction($node, $execution),
            NodeType::DatabaseAction->value => $this->evaluateDatabaseAction($node, $execution),
            NodeType::ScriptPlaceholder->value => $this->evaluateScriptPlaceholder($node, $execution),
            default => ['action' => 'skip'],
        };
    }

    public function findNextNodeId(string $currentNodeId, WorkflowExecution $execution): ?string
    {
        $version = WorkflowVersion::find($execution->workflow_version_id);
        if (! $version) {
            return null;
        }

        $edges = $version->edges ?? [];
        $nextEdge = collect($edges)->firstWhere('source', $currentNodeId);

        return $nextEdge['target'] ?? null;
    }

    public function findNextNodeIdWithCondition(string $currentNodeId, WorkflowExecution $execution, string $outcome): ?string
    {
        $version = WorkflowVersion::find($execution->workflow_version_id);
        if (! $version) {
            return null;
        }

        $edges = $version->edges ?? [];
        $matchingEdge = collect($edges)->first(function ($edge) use ($currentNodeId, $outcome) {
            return ($edge['source'] ?? null) === $currentNodeId
                && ($edge['label'] ?? null) === $outcome;
        });

        if ($matchingEdge) {
            return $matchingEdge['target'] ?? null;
        }

        $defaultEdge = collect($edges)->firstWhere('source', $currentNodeId);

        return $defaultEdge['target'] ?? null;
    }

    private function evaluateDecision(array $node, WorkflowExecution $execution): array
    {
        $config = $node['config'] ?? [];
        $field = $config['field'] ?? null;
        $variables = $execution->variables ?? [];
        $value = data_get($variables, $field);

        if ($value === null) {
            return ['action' => 'proceed', 'next' => $this->findNextNodeIdWithCondition($node['id'], $execution, 'default')];
        }

        return ['action' => 'proceed', 'next' => $this->findNextNodeIdWithCondition($node['id'], $execution, (string) $value)];
    }

    private function evaluateCondition(array $node, WorkflowExecution $execution): array
    {
        $config = $node['config'] ?? [];
        $conditions = $config['conditions'] ?? [];
        $variables = $execution->variables ?? [];
        $context = $execution->context ?? [];

        $allConditionsMet = true;

        foreach ($conditions as $condition) {
            $field = $condition['field'] ?? null;
            $operator = $condition['operator'] ?? 'equals';
            $expectedValue = $condition['value'] ?? null;

            $actualValue = data_get(array_merge($context, $variables), $field);

            $met = match ($operator) {
                'equals' => $actualValue == $expectedValue,
                'not_equals' => $actualValue != $expectedValue,
                'greater_than' => $actualValue > $expectedValue,
                'less_than' => $actualValue < $expectedValue,
                'contains' => str_contains((string) $actualValue, (string) $expectedValue),
                'in' => in_array($actualValue, (array) $expectedValue),
                'not_in' => ! in_array($actualValue, (array) $expectedValue),
                'empty' => empty($actualValue),
                'not_empty' => ! empty($actualValue),
                default => true,
            };

            if (! $met) {
                $allConditionsMet = false;
                break;
            }
        }

        $outcome = $allConditionsMet ? 'true' : 'false';

        return ['action' => 'proceed', 'next' => $this->findNextNodeIdWithCondition($node['id'], $execution, $outcome)];
    }

    private function evaluateApproval(array $node, WorkflowExecution $execution): array
    {
        $config = $node['config'] ?? [];
        $approvers = $config['approvers'] ?? [];
        $approvalsRequired = $config['approvals_required'] ?? 1;
        $approvalData = $execution->context['approvals'][$node['id']] ?? [];
        $approvedCount = collect($approvalData)->where('status', 'approved')->count();

        if ($approvedCount >= $approvalsRequired) {
            return ['action' => 'proceed', 'next' => $this->findNextNodeIdWithCondition($node['id'], $execution, 'approved')];
        }

        $rejectedCount = collect($approvalData)->where('status', 'rejected')->count();

        if ($rejectedCount > 0) {
            return ['action' => 'proceed', 'next' => $this->findNextNodeIdWithCondition($node['id'], $execution, 'rejected')];
        }

        return ['action' => 'wait', 'reason' => 'Awaiting approval'];
    }

    private function evaluateDelay(array $node, WorkflowExecution $execution): array
    {
        $config = $node['config'] ?? [];
        $delaySeconds = $config['delay_seconds'] ?? 0;
        $delayUntil = $config['delay_until'] ?? null;

        if ($delayUntil) {
            $targetTime = \Carbon\Carbon::parse($delayUntil);
            if (now()->isBefore($targetTime)) {
                return ['action' => 'wait', 'wait_until' => $targetTime->toIso8601String()];
            }
        }

        $startedAt = $execution->context['node_started_at'][$node['id']] ?? null;

        if ($startedAt) {
            $elapsed = now()->diffInSeconds(\Carbon\Carbon::parse($startedAt));
            if ($elapsed < $delaySeconds) {
                $remaining = $delaySeconds - $elapsed;

                return ['action' => 'wait', 'wait_seconds' => $remaining];
            }
        }

        return ['action' => 'proceed', 'next' => $this->findNextNodeId($node['id'], $execution)];
    }

    private function evaluateNotification(array $node, WorkflowExecution $execution): array
    {
        $config = $node['config'] ?? [];

        Log::info('Workflow Notification Node', [
            'node_id' => $node['id'],
            'config' => $config,
            'execution_id' => $execution->id,
        ]);

        return ['action' => 'proceed', 'next' => $this->findNextNodeId($node['id'], $execution)];
    }

    private function evaluateApiCall(array $node, WorkflowExecution $execution): array
    {
        $config = $node['config'] ?? [];

        Log::info('Workflow API Call Node (Extension Point)', [
            'node_id' => $node['id'],
            'url' => $config['url'] ?? null,
            'method' => $config['method'] ?? 'GET',
            'execution_id' => $execution->id,
        ]);

        return ['action' => 'proceed', 'next' => $this->findNextNodeId($node['id'], $execution)];
    }

    private function evaluateAiAction(array $node, WorkflowExecution $execution): array
    {
        $config = $node['config'] ?? [];

        Log::info('Workflow AI Action Node (Extension Point)', [
            'node_id' => $node['id'],
            'prompt' => $config['prompt'] ?? null,
            'execution_id' => $execution->id,
        ]);

        return ['action' => 'proceed', 'next' => $this->findNextNodeId($node['id'], $execution)];
    }

    private function evaluateDatabaseAction(array $node, WorkflowExecution $execution): array
    {
        $config = $node['config'] ?? [];

        Log::info('Workflow Database Action Node (Extension Point)', [
            'node_id' => $node['id'],
            'operation' => $config['operation'] ?? null,
            'execution_id' => $execution->id,
        ]);

        return ['action' => 'proceed', 'next' => $this->findNextNodeId($node['id'], $execution)];
    }

    private function evaluateScriptPlaceholder(array $node, WorkflowExecution $execution): array
    {
        Log::info('Workflow Script Placeholder Node (Extension Point)', [
            'node_id' => $node['id'],
            'execution_id' => $execution->id,
        ]);

        return ['action' => 'proceed', 'next' => $this->findNextNodeId($node['id'], $execution)];
    }

    private function findNextNode(array $node, WorkflowExecution $execution): ?string
    {
        return $this->findNextNodeId($node['id'], $execution);
    }
}
