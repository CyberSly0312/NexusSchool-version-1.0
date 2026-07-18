<?php

return [

    'settings' => [
        'cache_ttl' => (int) env('SYSTEM_SETTINGS_CACHE_TTL', 3600),
        'encryption_key_env' => env('SYSTEM_ENCRYPTION_KEY'),
    ],

    'audit' => [
        'enabled' => (bool) env('SYSTEM_AUDIT_ENABLED', true),
        'retention_days' => (int) env('SYSTEM_AUDIT_RETENTION_DAYS', 365),
        'queue' => env('SYSTEM_AUDIT_QUEUE', 'default'),
    ],

    'activity' => [
        'enabled' => (bool) env('SYSTEM_ACTIVITY_ENABLED', true),
        'retention_days' => (int) env('SYSTEM_ACTIVITY_RETENTION_DAYS', 90),
        'log_name' => env('SYSTEM_ACTIVITY_LOG_NAME', 'default'),
    ],

    'health' => [
        'check_interval_minutes' => (int) env('SYSTEM_HEALTH_CHECK_INTERVAL', 5),
        'notification_threshold' => env('SYSTEM_HEALTH_NOTIFICATION_THRESHOLD', 'unhealthy'),
        'services' => [
            'database' => ['enabled' => true, 'timeout' => 5],
            'cache' => ['enabled' => true, 'timeout' => 5],
            'queue' => ['enabled' => true, 'timeout' => 5],
            'storage' => ['enabled' => true, 'timeout' => 5],
            'mail' => ['enabled' => true, 'timeout' => 5],
            'scheduler' => ['enabled' => true, 'timeout' => 5],
        ],
    ],

    'backup' => [
        'default_disk' => env('SYSTEM_BACKUP_DISK', 'local'),
        'max_concurrent' => (int) env('SYSTEM_BACKUP_MAX_CONCURRENT', 1),
        'retention_count' => (int) env('SYSTEM_BACKUP_RETENTION', 10),
        'scheduled_enabled' => (bool) env('SYSTEM_BACKUP_SCHEDULED', false),
        'scheduled_cron' => env('SYSTEM_BACKUP_CRON', '0 2 * * *'),
    ],

    'maintenance' => [
        'max_duration_hours' => (int) env('SYSTEM_MAINTENANCE_MAX_DURATION', 24),
        'prevent_overlap' => (bool) env('SYSTEM_MAINTENANCE_PREVENT_OVERLAP', true),
    ],

    'cleanup' => [
        'audit_retention_days' => (int) env('SYSTEM_CLEANUP_AUDIT_DAYS', 365),
        'activity_retention_days' => (int) env('SYSTEM_CLEANUP_ACTIVITY_DAYS', 90),
        'health_retention_days' => (int) env('SYSTEM_CLEANUP_HEALTH_DAYS', 30),
    ],

    'cache' => [
        'enabled' => (bool) env('SYSTEM_CACHE_ENABLED', true),
        'ttl_minutes' => (int) env('SYSTEM_CACHE_TTL', 60),
        'prefix' => 'system:',
    ],
];
