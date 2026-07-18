# Workflow & Automation Engine

## Overview

The Workflow & Automation Engine provides a complete workflow execution and automation system for the Nexus School ERP. It supports custom workflows, automation rules, scheduled workflows, event-driven workflows, approval chains, and both manual and automatic tasks.

## Architecture

### Domain Structure

```
app/Domains/Workflow/
├── DTOs/
│   ├── AutomationRuleData.php
│   ├── WorkflowData.php
│   ├── WorkflowExecutionData.php
│   └── WorkflowTaskData.php
│   └── WorkflowVersionData.php
├── Enums/
│   ├── ExecutionStatus.php
│   ├── NodeType.php
│   ├── TaskStatus.php
│   ├── TaskType.php
│   ├── TriggerType.php
│   ├── WorkflowStatus.php
│   └── WorkflowVersionStatus.php
├── Events/
│   ├── AutomationTriggered.php
│   ├── TaskAssigned.php
│   ├── TaskCompleted.php
│   ├── WorkflowCompleted.php
│   ├── WorkflowCreated.php
│   ├── WorkflowFailed.php
│   ├── WorkflowPublished.php
│   └── WorkflowStarted.php
├── Listeners/
│   ├── AiIntegrationListener.php
│   ├── AnalyticsCollector.php
│   ├── AuditLogger.php
│   ├── NotificationDispatcher.php
│   └── QueueDispatcher.php
├── Policies/
│   ├── AutomationRulePolicy.php
│   ├── WorkflowExecutionPolicy.php
│   └── WorkflowPolicy.php
├── Rules/
│   ├── AutomationRuleRules.php
│   ├── WorkflowExecutionRules.php
│   ├── WorkflowRules.php
│   ├── WorkflowTriggerRules.php
│   └── WorkflowVersionRules.php
└── Services/
    ├── AutomationService.php
    ├── ExecutionService.php
    ├── RuleEngineService.php
    ├── ScheduleService.php
    └── WorkflowService.php
```

### Models

| Model | Description |
|-------|-------------|
| `Workflow` | Main workflow definition with versioning |
| `WorkflowVersion` | Immutable workflow version with node/edge graph |
| `WorkflowTrigger` | Event, time, manual, API, or scheduled triggers |
| `WorkflowExecution` | Runtime execution instance with audit trail |
| `WorkflowTask` | Individual task within an execution |
| `WorkflowSchedule` | Cron-based scheduled workflow triggers |
| `AutomationRule` | Event-driven automation rules with conditions and actions |

### Enums

| Enum | Values |
|------|--------|
| `WorkflowStatus` | `draft`, `active`, `inactive`, `archived` |
| `WorkflowVersionStatus` | `draft`, `published`, `archived` |
| `NodeType` | `start`, `end`, `decision`, `condition`, `approval`, `notification`, `delay`, `api_call`, `ai_action`, `database_action`, `script_placeholder` |
| `TriggerType` | `event`, `time`, `manual`, `api`, `scheduled` |
| `ExecutionStatus` | `pending`, `running`, `paused`, `waiting_approval`, `completed`, `failed`, `cancelled`, `timed_out` |
| `TaskStatus` | `pending`, `assigned`, `in_progress`, `completed`, `failed`, `skipped`, `cancelled` |
| `TaskType` | `approval`, `review`, `notification`, `data_entry`, `api_call`, `ai_action`, `database_action`, `script_placeholder` |

## Services

### WorkflowService
- **create()**: Create workflow with initial version
- **update()**: Update workflow metadata
- **publish()**: Publish draft version (validates graph)
- **updateVersion()**: Update unpublished version nodes/edges
- **archive()**: Archive workflow
- **delete()**: Soft delete (skips if executions exist)
- **validateWorkflowGraph()**: Checks for start/end nodes and circular dependencies

### ExecutionService
- **startExecution()**: Start workflow execution with idempotency support
- **completeExecution()**: Mark execution as completed
- **failExecution()**: Mark execution as failed
- **cancelExecution()**: Cancel running execution and pending tasks
- **retryExecution()**: Retry failed/timed-out execution
- **completeTask()**: Complete a task and check execution progress
- **assignTask()**: Assign task to user with event dispatch

### RuleEngineService
- **evaluateNode()**: Route to type-specific evaluator
- **evaluateDecision()**: Decision node with variable-based branching
- **evaluateCondition()**: Multi-condition evaluation with operators
- **evaluateApproval()**: Approval chain with configurable required count
- **evaluateDelay()**: Time-based delay with elapsed tracking
- Extension points: `evaluateApiCall()`, `evaluateAiAction()`, `evaluateDatabaseAction()`, `evaluateScriptPlaceholder()`

### AutomationService
- **create()**, **update()**, **enable()**, **disable()**, **delete()**
- **triggerRules()**: Find and execute matching rules for an event
- **shouldTrigger()**: Cooldown and condition checking
- **evaluateConditions()**: Support for `equals`, `not_equals`, `greater_than`, `less_than`, `contains`, `in`, `not_in`, `empty`, `not_empty`

### ScheduleService
- **createSchedule()**: Create cron-based schedule with validation
- **getDueSchedules()**: Find schedules ready to execute
- **processSchedule()**: Dispatch execution and update schedule state

## Workflow Graph

Workflows are defined as directed graphs with nodes and edges:

```json
{
  "nodes": [
    {"id": "start", "type": "start", "name": "Start"},
    {"id": "approval-1", "type": "approval", "name": "Manager Approval", "config": {"approvers": ["manager"], "approvals_required": 1}},
    {"id": "end", "type": "end", "name": "End"}
  ],
  "edges": [
    {"id": "e1", "source": "start", "target": "approval-1"},
    {"id": "e2", "source": "approval-1", "target": "end", "label": "approved"}
  ]
}
```

### Node Types
- **start/end**: Mandatory entry and exit points
- **decision**: Variable-based branching
- **condition**: Multi-condition evaluation
- **approval**: Human approval with configurable approvers
- **notification**: Send notifications
- **delay**: Time-based delays
- **api_call**: External API integration (extension point)
- **ai_action**: AI-powered actions (extension point)
- **database_action**: Database operations (extension point)
- **script_placeholder**: Custom script execution (extension point)

## Cross-Module Integration

### Events
| Event | Dispatched When |
|-------|-----------------|
| `WorkflowCreated` | New workflow created |
| `WorkflowPublished` | Draft version published |
| `WorkflowStarted` | Execution started |
| `WorkflowCompleted` | Execution completed successfully |
| `WorkflowFailed` | Execution failed |
| `TaskAssigned` | Task assigned to user |
| `TaskCompleted` | Task completed |
| `AutomationTriggered` | Automation rule executed |

### Listeners
| Listener | Purpose |
|----------|---------|
| `AuditLogger` | Logs all workflow events for audit trail |
| `NotificationDispatcher` | Dispatches notifications for workflow events |
| `QueueDispatcher` | Queue-based workflow node execution |
| `AnalyticsCollector` | Collects analytics data for workflow events |
| `AiIntegrationListener` | AI platform integration for AI action nodes |

### Queue Jobs
| Job | Purpose |
|-----|---------|
| `ExecuteWorkflowNode` | Process individual workflow node execution |
| `ProcessScheduledWorkflow` | Process scheduled workflow triggers |
| `TriggerAutomationRules` | Async automation rule evaluation |

### Console Commands
| Command | Description |
|---------|-------------|
| `workflow:process-schedules` | Process all due workflow schedules |
| `workflow:cleanup-stale` | Mark stale running executions as timed out |

## Security

- **Tenant isolation**: All tables have `school_id` foreign key
- **Permission-based**: `workflow.view`, `workflow.create`, `workflow.update`, `workflow.delete`, `workflow.publish`, `workflow.execute`, `workflow.manage`
- **Idempotency**: Duplicate execution prevention via `idempotency_key`
- **Approval authorization**: Configurable approval chains
- **Audit logging**: All events logged via `AuditLogger` listener
- **Graph validation**: Circular dependency detection, mandatory start/end nodes

## Performance

- Queue-based execution via `ExecuteWorkflowNode` job
- Scheduled job processing via `ProcessScheduledWorkflow`
- Cooldown-based rate limiting on automation rules
- Stale execution cleanup via console command
- Configurable max concurrent executions

## Configuration

Config file: `config/workflow.php`

Key settings:
- `default.max_retries`: Maximum retry count for failed executions
- `default.timeout_seconds`: Execution timeout
- `execution.queue`: Queue name for workflow execution jobs
- `automation.max_rules_per_event`: Max automation rules per event
- `security.require_approval_authorization`: Require authorization for approvals
