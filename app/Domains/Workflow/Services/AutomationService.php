<?php

declare(strict_types=1);

namespace App\Domains\Workflow\Services;

use App\Domains\Workflow\DTOs\AutomationRuleData;
use App\Domains\Workflow\Events\AutomationTriggered;
use App\Domains\Workflow\Rules\AutomationRuleRules;
use App\Models\AutomationRule;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutomationService
{
    public function __construct(
        protected AutomationRule $model = new AutomationRule(),
    ) {
    }

    public function create(array $data): AutomationRuleData
    {
        $validated = AutomationRuleRules::validate($data, 'create');

        return DB::transaction(function () use ($validated) {
            $rule = $this->model->create($validated);

            return AutomationRuleData::create($rule->toArray());
        });
    }

    public function update(AutomationRule $rule, array $data): AutomationRuleData
    {
        $validated = AutomationRuleRules::validate($data, 'update', $rule->id);

        $rule->update($validated);

        return AutomationRuleData::create($rule->fresh()->toArray());
    }

    public function enable(AutomationRule $rule): AutomationRuleData
    {
        $rule->update(['is_active' => true]);

        return AutomationRuleData::create($rule->fresh()->toArray());
    }

    public function disable(AutomationRule $rule): AutomationRuleData
    {
        $rule->update(['is_active' => false]);

        return AutomationRuleData::create($rule->fresh()->toArray());
    }

    public function delete(AutomationRule $rule): void
    {
        $rule->delete();
    }

    public function triggerRules(string $eventName, array $context = []): array
    {
        $rules = $this->model
            ->active()
            ->where('trigger_type', 'event')
            ->where('event_name', $eventName)
            ->orderBy('priority', 'desc')
            ->get();

        $triggered = [];

        foreach ($rules as $rule) {
            if ($this->shouldTrigger($rule, $context)) {
                $this->executeRule($rule, $context);
                $triggered[] = AutomationRuleData::create($rule->fresh()->toArray());
            }
        }

        return $triggered;
    }

    public function findById(string $id): AutomationRule
    {
        return $this->model->findOrFail($id);
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (isset($filters['school_id'])) {
            $query->where('school_id', $filters['school_id']);
        }

        if (isset($filters['trigger_type'])) {
            $query->where('trigger_type', $filters['trigger_type']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (isset($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        return $query->orderBy('priority', 'desc')->paginate($perPage);
    }

    private function shouldTrigger(AutomationRule $rule, array $context): bool
    {
        if (! $rule->is_active) {
            return false;
        }

        if ($rule->cooldown_seconds > 0 && $rule->last_executed_at) {
            $cooldownEnd = $rule->last_executed_at->addSeconds($rule->cooldown_seconds);
            if (now()->isBefore($cooldownEnd)) {
                return false;
            }
        }

        $conditions = $rule->conditions;
        if ($conditions && ! empty($conditions)) {
            return $this->evaluateConditions($conditions, $context);
        }

        return true;
    }

    private function evaluateConditions(array $conditions, array $context): bool
    {
        foreach ($conditions as $condition) {
            $field = $condition['field'] ?? null;
            $operator = $condition['operator'] ?? 'equals';
            $value = $condition['value'] ?? null;

            if (! $field) {
                continue;
            }

            $actual = data_get($context, $field);

            $result = match ($operator) {
                'equals' => $actual == $value,
                'not_equals' => $actual != $value,
                'greater_than' => $actual > $value,
                'less_than' => $actual < $value,
                'contains' => str_contains((string) $actual, (string) $value),
                'starts_with' => str_starts_with((string) $actual, (string) $value),
                'ends_with' => str_ends_with((string) $actual, (string) $value),
                'in' => in_array($actual, (array) $value),
                'not_in' => ! in_array($actual, (array) $value),
                'empty' => empty($actual),
                'not_empty' => ! empty($actual),
                default => true,
            };

            if (! $result) {
                return false;
            }
        }

        return true;
    }

    private function executeRule(AutomationRule $rule, array $context): void
    {
        $rule->update([
            'execution_count' => $rule->execution_count + 1,
            'last_executed_at' => now(),
        ]);

        AutomationTriggered::dispatch($rule, $context);

        foreach ($rule->actions as $action) {
            $actionType = $action['type'] ?? null;

            Log::info("Automation Rule Action: {$actionType}", [
                'rule_id' => $rule->id,
                'action' => $action,
                'context' => $context,
            ]);
        }

        $rule->update([
            'success_count' => $rule->success_count + 1,
        ]);
    }
}
