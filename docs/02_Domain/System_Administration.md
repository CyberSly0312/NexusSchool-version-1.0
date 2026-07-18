# System Administration & Monitoring Engine

## Overview

The System Administration & Monitoring Engine provides comprehensive system management, health monitoring, audit logging, backup management, and maintenance window support for the Nexus School ERP.

## Architecture

### Domain Structure

```
app/Domains/System/
├── DTOs/
│   ├── AuditLogData.php
│   ├── BackupData.php
│   ├── MaintenanceWindowData.php
│   ├── SystemHealthData.php
│   └── SystemSettingData.php
├── Enums/
│   ├── ActivityType.php
│   ├── AuditAction.php
│   ├── BackupStatus.php
│   ├── BackupType.php
│   ├── HealthStatus.php
│   ├── MaintenanceType.php
│   └── SettingValueType.php
├── Events/
│   ├── AuditRecorded.php
│   ├── BackupCreated.php
│   ├── BackupRestored.php
│   ├── HealthCheckFailed.php
│   ├── MaintenanceEnded.php
│   ├── MaintenanceStarted.php
│   └── SystemSettingUpdated.php
├── Listeners/
│   ├── AnalyticsCollector.php
│   ├── AuditLogger.php
│   ├── CacheRefresher.php
│   ├── HealthMonitor.php
│   └── NotificationDispatcher.php
├── Policies/
│   ├── AuditLogPolicy.php
│   ├── BackupPolicy.php
│   ├── MaintenanceWindowPolicy.php
│   ├── NotificationChannelPolicy.php
│   └── SystemSettingPolicy.php
├── Rules/
│   ├── BackupRules.php
│   ├── MaintenanceWindowRules.php
│   └── SystemSettingRules.php
└── Services/
    ├── ActivityService.php
    ├── AuditService.php
    ├── BackupService.php
    ├── HealthCheckService.php
    ├── MaintenanceService.php
    ├── MonitoringService.php
    └── SystemAdministrationService.php
```

### Models

| Model | Description |
|-------|-------------|
| `SystemSetting` | Global and school-specific settings with encryption support |
| `AuditLog` | Immutable audit trail for all entity changes |
| `ActivityLog` | Immutable activity log for user and system actions |
| `ScheduledTask` | Background task scheduler with cron expressions |
| `Backup` | Backup history with status tracking |
| `SystemHealth` | Health check results for all monitored services |
| `MaintenanceWindow` | Scheduled and emergency maintenance windows |
| `NotificationChannel` | Configurable notification delivery channels |

## Services

### SystemAdministrationService
- **create()**: Create setting with duplicate key validation
- **update()**: Update with read-only and encryption checks
- **get()**: Retrieve setting with cache and school override support
- **set()**: Set or create setting by group/key
- **getGroup()**: Retrieve all settings in a group
- **delete()**: Soft delete with system setting protection

### AuditService
- **record()**: Create immutable audit log entry with event dispatch
- **getForModel()**: Get audit history for any model
- **paginate()**: Filter by action, type, user, date range

### ActivityService
- **record()**: Log activity with causer/subject tracking
- **getForModel()**: Get activity history for any model

### MonitoringService
- **checkDatabase()**: Database connectivity and response time
- **checkCache()**: Cache read/write verification
- **checkQueue()**: Queue driver connectivity
- **checkStorage()**: Disk usage monitoring
- **checkMail()**: Mail driver configuration check
- **checkScheduler()**: Scheduler status check
- **getLatestHealthChecks()**: Latest status per service
- **getHealthHistory()**: Historical health data

### HealthCheckService
- **runAllChecks()**: Execute all enabled health checks
- **runSingleCheck()**: Check specific service
- **getStatus()**: Overall system status
- **getSystemMetrics()**: PHP/Laravel/disk metrics

### BackupService
- **create()**: Create backup record
- **executeBackup()**: Execute backup with table enumeration
- **restore()**: Restore from backup (dispatches event)
- **verify()**: Checksum verification
- **Concurrent prevention**: Only one backup at a time

### MaintenanceService
- **create()**: Create with overlap detection
- **start()**: Activate maintenance window
- **end()**: Complete maintenance window
- **isActive()**: Check if maintenance is active
- **isReadonly()**: Check if system is in read-only mode

## Health Monitoring

Monitored services:
| Service | Check Method |
|---------|-------------|
| Database | PDO connection test |
| Cache | Write/read/delete cycle |
| Queue | Driver connection test |
| Storage | Disk usage percentage |
| Mail | Driver configuration |
| Scheduler | Last run timestamp |

### Health Statuses
- **Healthy**: Service operational
- **Degraded**: Service slow or partially impaired
- **Unhealthy**: Service down or non-functional
- **Unknown**: Not yet checked

## Backup Management

### Backup Lifecycle
1. **Pending**: Backup record created
2. **Running**: Backup in progress
3. **Completed**: Backup successful with checksum
4. **Verified**: Checksum verified against file
5. **Failed**: Backup failed with error

### Features
- Manual and scheduled backup types
- Configurable table selection
- Checksum verification
- Concurrent execution prevention
- Local storage (no cloud integration per requirements)

## Maintenance Windows

### Lifecycle
1. **Pending**: Window scheduled
2. **Active**: Maintenance in progress
3. **Completed**: Maintenance finished

### Features
- Scheduled, emergency, and rolling types
- Overlap prevention
- Read-only mode support
- Affected services tracking
- Actual start/end time tracking

## Security

- **Super Admin only**: System settings, backups, maintenance windows
- **Immutable logs**: Audit and activity logs cannot be modified
- **Encryption**: Sensitive settings encrypted via Laravel Crypt
- **Read-only protection**: System settings cannot be deleted
- **Audit trail**: All configuration changes tracked

## Console Commands

| Command | Description |
|---------|-------------|
| `system:health-check` | Run health checks (all or specific service) |
| `system:backup` | Create manual backup |
| `system:cleanup` | Remove old audit/activity/health records |
| `system:maintenance` | Start/end maintenance windows |

## Configuration

Config file: `config/system.php`

Key settings:
- `health.check_interval_minutes`: Health check frequency
- `backup.max_concurrent`: Concurrent backup limit
- `maintenance.max_duration_hours`: Maximum maintenance duration
- `cleanup.audit_retention_days`: Audit log retention
