<?php

return [

    'default' => [
        'max_retries' => (int) env('WORKFLOW_MAX_RETRIES', 3),
        'timeout_seconds' => (int) env('WORKFLOW_TIMEOUT_SECONDS', 3600),
        'max_concurrent_executions' => (int) env('WORKFLOW_MAX_CONCURRENT', 100),
    ],

    'execution' => [
        'queue' => env('WORKFLOW_EXECUTION_QUEUE', 'default'),
        'timeout' => (int) env('WORKFLOW_EXECUTION_TIMEOUT', 120),
        'retry_after' => (int) env('WORKFLOW_RETRY_AFTER', 60),
    ],

    'schedules' => [
        'queue' => env('WORKFLOW_SCHEDULE_QUEUE', 'default'),
        'check_interval_minutes' => (int) env('WORKFLOW_SCHEDULE_CHECK_INTERVAL', 1),
    ],

    'nodes' => [
        'delay' => [
            'max_seconds' => (int) env('WORKFLOW_DELAY_MAX_SECONDS', 86400),
        ],
        'ai_action' => [
            'timeout' => (int) env('WORKFLOW_AI_ACTION_TIMEOUT', 120),
        ],
        'api_call' => [
            'timeout' => (int) env('WORKFLOW_API_CALL_TIMEOUT', 30),
            'max_retries' => (int) env('WORKFLOW_API_CALL_RETRIES', 3),
        ],
    ],

    'automation' => [
        'max_rules_per_event' => (int) env('WORKFLOW_MAX_RULES_PER_EVENT', 50),
        'default_cooldown_seconds' => (int) env('WORKFLOW_DEFAULT_COOLDOWN', 0),
    ],

    'cache' => [
        'enabled' => (bool) env('WORKFLOW_CACHE_ENABLED', true),
        'ttl_minutes' => (int) env('WORKFLOW_CACHE_TTL', 60),
        'prefix' => 'workflow:',
    ],

    'security' => [
        'require_approval_authorization' => (bool) env('WORKFLOW_REQUIRE_APPROVAL_AUTH', true),
        'audit_all_executions' => (bool) env('WORKFLOW_AUDIT_EXECUTIONS', true),
        'restrict_api_triggers' => (bool) env('WORKFLOW_RESTRICT_API_TRIGGERS', true),
    ],
];
