<?php

declare(strict_types=1);

namespace App\Providers;

use App\Domains\AcademicClass\Events\AcademicClassActivated;
use App\Domains\AcademicClass\Events\AcademicClassArchived;
use App\Domains\AcademicClass\Events\AcademicClassCreated;
use App\Domains\AcademicClass\Events\AcademicClassUpdated;
use App\Domains\AcademicClass\Listeners\LogAcademicClassActivated;
use App\Domains\AcademicClass\Listeners\LogAcademicClassArchived;
use App\Domains\AcademicClass\Listeners\LogAcademicClassCreated;
use App\Domains\AcademicClass\Listeners\LogAcademicClassUpdated;
use App\Domains\AcademicTerm\Events\AcademicTermActivated;
use App\Domains\AcademicTerm\Events\AcademicTermArchived;
use App\Domains\AcademicTerm\Events\AcademicTermCreated;
use App\Domains\AcademicTerm\Events\AcademicTermStatusChanged;
use App\Domains\AcademicTerm\Events\AcademicTermUpdated;
use App\Domains\AcademicTerm\Listeners\LogAcademicTermActivated;
use App\Domains\AcademicTerm\Listeners\LogAcademicTermArchived;
use App\Domains\AcademicTerm\Listeners\LogAcademicTermCreated;
use App\Domains\AcademicTerm\Listeners\LogAcademicTermStatusChanged;
use App\Domains\AcademicTerm\Listeners\LogAcademicTermUpdated;
use App\Domains\AcademicYear\Events\AcademicYearActivated;
use App\Domains\AcademicYear\Events\AcademicYearArchived;
use App\Domains\AcademicYear\Events\AcademicYearCloned;
use App\Domains\AcademicYear\Events\AcademicYearCreated;
use App\Domains\AcademicYear\Events\AcademicYearDatesChanged;
use App\Domains\AcademicYear\Events\AcademicYearDeleted;
use App\Domains\AcademicYear\Events\AcademicYearStatusChanged;
use App\Domains\AcademicYear\Events\AcademicYearSwitched;
use App\Domains\AcademicYear\Events\AcademicYearUpdated;
use App\Domains\AcademicYear\Listeners\LogAcademicYearActivated;
use App\Domains\AcademicYear\Listeners\LogAcademicYearArchived;
use App\Domains\AcademicYear\Listeners\LogAcademicYearCloned;
use App\Domains\AcademicYear\Listeners\LogAcademicYearCreated;
use App\Domains\AcademicYear\Listeners\LogAcademicYearDatesChanged;
use App\Domains\AcademicYear\Listeners\LogAcademicYearDeleted;
use App\Domains\AcademicYear\Listeners\LogAcademicYearStatusChanged;
use App\Domains\AcademicYear\Listeners\LogAcademicYearSwitched;
use App\Domains\AcademicYear\Listeners\LogAcademicYearUpdated;
use App\Domains\Ai\Events\ConversationStarted;
use App\Domains\Ai\Events\KnowledgeIndexed;
use App\Domains\Ai\Events\MessageGenerated;
use App\Domains\Ai\Events\PromptTemplateUpdated;
use App\Domains\Ai\Events\ProviderChanged;
use App\Domains\Ai\Events\UsageQuotaExceeded;
use App\Domains\Ai\Listeners\AnalyticsCollector as AiAnalyticsCollector;
use App\Domains\Ai\Listeners\AuditLogger as AiAuditLogger;
use App\Domains\Ai\Listeners\CacheClearer as AiCacheClearer;
use App\Domains\Ai\Listeners\NotificationDispatcher as AiNotificationDispatcher;
use App\Domains\Ai\Listeners\UsageTracker;
use App\Domains\Analytics\Events\DashboardCreated;
use App\Domains\Analytics\Events\KpiRecalculated;
use App\Domains\Analytics\Events\ReportGenerated;
use App\Domains\Analytics\Events\ReportScheduled;
use App\Domains\Analytics\Events\SnapshotCreated;
use App\Domains\Analytics\Events\WidgetAdded;
use App\Domains\Analytics\Listeners\AuditLogger as AnalyticsAuditLogger;
use App\Domains\Analytics\Listeners\CacheRefresher;
use App\Domains\Analytics\Listeners\ExportGenerator;
use App\Domains\Analytics\Listeners\NotificationDispatcher as AnalyticsNotificationDispatcher;
use App\Domains\Api\Events\ApiClientDisabled;
use App\Domains\Api\Events\ApiTokenCreated;
use App\Domains\Api\Events\ApiTokenRevoked;
use App\Domains\Api\Events\WebhookDelivered;
use App\Domains\Api\Events\WebhookFailed;
use App\Domains\Api\Events\WebhookRegistered;
use App\Domains\Api\Listeners\AuditLogger as ApiAuditLogger;
use App\Domains\Api\Listeners\CacheClearer as ApiCacheClearer;
use App\Domains\Api\Listeners\NotificationDispatcher as ApiNotificationDispatcher;
use App\Domains\Api\Listeners\RetryDispatcher;
use App\Domains\Classroom\Events\ClassroomAssigned;
use App\Domains\Classroom\Events\ClassroomCreated;
use App\Domains\Classroom\Events\ClassroomReleased;
use App\Domains\Classroom\Events\ClassroomUpdated;
use App\Domains\Classroom\Listeners\LogClassroomAssigned;
use App\Domains\Classroom\Listeners\LogClassroomCreated;
use App\Domains\Classroom\Listeners\LogClassroomReleased;
use App\Domains\Classroom\Listeners\LogClassroomUpdated;
use App\Domains\Communication\Events\AnnouncementPublished;
use App\Domains\Communication\Events\MessageSent;
use App\Domains\Communication\Events\NotificationDelivered;
use App\Domains\Communication\Events\NotificationFailed;
use App\Domains\Communication\Events\NotificationQueued;
use App\Domains\Communication\Events\NotificationSent;
use App\Domains\Communication\Listeners\AnalyticsIntegration;
use App\Domains\Communication\Listeners\AuditLogger;
use App\Domains\Communication\Listeners\CacheClearer;
use App\Domains\Communication\Listeners\DeliveryTracker;
use App\Domains\Communication\Listeners\QueueDispatcher;
use App\Domains\Department\Events\DepartmentArchived;
use App\Domains\Department\Events\DepartmentCreated;
use App\Domains\Department\Events\DepartmentUpdated;
use App\Domains\Department\Listeners\LogDepartmentArchived;
use App\Domains\Department\Listeners\LogDepartmentCreated;
use App\Domains\Department\Listeners\LogDepartmentUpdated;
use App\Domains\GradeLevel\Events\GradeLevelArchived;
use App\Domains\GradeLevel\Events\GradeLevelCreated;
use App\Domains\GradeLevel\Events\GradeLevelUpdated;
use App\Domains\GradeLevel\Listeners\LogGradeLevelArchived;
use App\Domains\GradeLevel\Listeners\LogGradeLevelCreated;
use App\Domains\GradeLevel\Listeners\LogGradeLevelUpdated;
use App\Domains\Guardian\Events\GuardianActivated;
use App\Domains\Guardian\Events\GuardianArchived;
use App\Domains\Guardian\Events\GuardianCreated;
use App\Domains\Guardian\Events\GuardianUpdated;
use App\Domains\Guardian\Listeners\LogGuardianActivated;
use App\Domains\Guardian\Listeners\LogGuardianArchived;
use App\Domains\Guardian\Listeners\LogGuardianCreated;
use App\Domains\Guardian\Listeners\LogGuardianUpdated;
use App\Domains\Library\Events\BookBorrowed;
use App\Domains\Library\Events\BookCreated;
use App\Domains\Library\Events\BookReturned;
use App\Domains\Library\Events\FineGenerated;
use App\Domains\Library\Events\FinePaid;
use App\Domains\Library\Events\ReservationCreated;
use App\Domains\Library\Events\ReservationExpired;
use App\Domains\Library\Listeners\LogBookBorrowed;
use App\Domains\Library\Listeners\LogBookCreated;
use App\Domains\Library\Listeners\LogBookReturned;
use App\Domains\Library\Listeners\LogFineGenerated;
use App\Domains\Library\Listeners\LogFinePaid;
use App\Domains\Library\Listeners\LogReservationCreated;
use App\Domains\Library\Listeners\LogReservationExpired;
use App\Domains\Permission\Events\PermissionArchived;
use App\Domains\Permission\Events\PermissionAssignedToRole;
use App\Domains\Permission\Events\PermissionCreated;
use App\Domains\Permission\Events\PermissionRevokedFromRole;
use App\Domains\Permission\Events\PermissionUpdated;
use App\Domains\Permission\Listeners\LogPermissionArchived;
use App\Domains\Permission\Listeners\LogPermissionAssignedToRole;
use App\Domains\Permission\Listeners\LogPermissionCreated;
use App\Domains\Permission\Listeners\LogPermissionRevokedFromRole;
use App\Domains\Permission\Listeners\LogPermissionUpdated;
use App\Domains\Program\Events\ProgramArchived;
use App\Domains\Program\Events\ProgramCreated;
use App\Domains\Program\Events\ProgramUpdated;
use App\Domains\Program\Listeners\LogProgramArchived;
use App\Domains\Program\Listeners\LogProgramCreated;
use App\Domains\Program\Listeners\LogProgramUpdated;
use App\Domains\Role\Events\RoleAssignedToUser;
use App\Domains\Role\Events\RoleCreated;
use App\Domains\Role\Events\RoleRevokedFromUser;
use App\Domains\Role\Events\RoleUpdated;
use App\Domains\Section\Events\SectionArchived;
use App\Domains\Section\Events\SectionCreated;
use App\Domains\Section\Events\SectionUpdated;
use App\Domains\Section\Listeners\LogSectionArchived;
use App\Domains\Section\Listeners\LogSectionCreated;
use App\Domains\Section\Listeners\LogSectionUpdated;
use App\Domains\Student\Events\StudentActivated;
use App\Domains\Student\Events\StudentArchived;
use App\Domains\Student\Events\StudentCreated;
use App\Domains\Student\Events\StudentUpdated;
use App\Domains\Student\Listeners\LogStudentActivated;
use App\Domains\Student\Listeners\LogStudentArchived;
use App\Domains\Student\Listeners\LogStudentCreated;
use App\Domains\Student\Listeners\LogStudentUpdated;
use App\Domains\StudentEnrollment\Events\EnrollmentActivated;
use App\Domains\StudentEnrollment\Events\EnrollmentCompleted;
use App\Domains\StudentEnrollment\Events\EnrollmentSuspended;
use App\Domains\StudentEnrollment\Events\EnrollmentWithdrawn;
use App\Domains\StudentEnrollment\Events\StudentEnrolled;
use App\Domains\StudentEnrollment\Listeners\LogEnrollmentActivated;
use App\Domains\StudentEnrollment\Listeners\LogEnrollmentCompleted;
use App\Domains\StudentEnrollment\Listeners\LogEnrollmentSuspended;
use App\Domains\StudentEnrollment\Listeners\LogEnrollmentWithdrawn;
use App\Domains\StudentEnrollment\Listeners\LogStudentEnrolled;
use App\Domains\Subject\Events\SubjectActivated;
use App\Domains\Subject\Events\SubjectArchived;
use App\Domains\Subject\Events\SubjectCreated;
use App\Domains\Subject\Events\SubjectUpdated;
use App\Domains\Subject\Listeners\LogSubjectActivated;
use App\Domains\Subject\Listeners\LogSubjectArchived;
use App\Domains\Subject\Listeners\LogSubjectCreated;
use App\Domains\Subject\Listeners\LogSubjectUpdated;
use App\Domains\SubjectCategory\Events\SubjectCategoryArchived;
use App\Domains\SubjectCategory\Events\SubjectCategoryCreated;
use App\Domains\SubjectCategory\Events\SubjectCategoryUpdated;
use App\Domains\SubjectCategory\Listeners\LogSubjectCategoryArchived;
use App\Domains\SubjectCategory\Listeners\LogSubjectCategoryCreated;
use App\Domains\SubjectCategory\Listeners\LogSubjectCategoryUpdated;
use App\Domains\SubjectCoefficient\Events\SubjectCoefficientActivated;
use App\Domains\SubjectCoefficient\Events\SubjectCoefficientArchived;
use App\Domains\SubjectCoefficient\Events\SubjectCoefficientCreated;
use App\Domains\SubjectCoefficient\Events\SubjectCoefficientUpdated;
use App\Domains\SubjectCoefficient\Listeners\LogSubjectCoefficientActivated;
use App\Domains\SubjectCoefficient\Listeners\LogSubjectCoefficientArchived;
use App\Domains\SubjectCoefficient\Listeners\LogSubjectCoefficientCreated;
use App\Domains\SubjectCoefficient\Listeners\LogSubjectCoefficientUpdated;
use App\Domains\System\Events\AuditRecorded;
use App\Domains\System\Events\BackupCreated;
use App\Domains\System\Events\BackupRestored;
use App\Domains\System\Events\HealthCheckFailed;
use App\Domains\System\Events\MaintenanceEnded;
use App\Domains\System\Events\MaintenanceStarted;
use App\Domains\System\Events\SystemSettingUpdated;
use App\Domains\System\Listeners\AnalyticsCollector as SystemAnalyticsCollector;
use App\Domains\System\Listeners\AuditLogger as SystemAuditLogger;
use App\Domains\System\Listeners\CacheRefresher as SystemCacheRefresher;
use App\Domains\System\Listeners\HealthMonitor;
use App\Domains\System\Listeners\NotificationDispatcher as SystemNotificationDispatcher;
use App\Domains\Transport\Events\DriverAssigned;
use App\Domains\Transport\Events\DriverUpdated;
use App\Domains\Transport\Events\FuelLogged;
use App\Domains\Transport\Events\MaintenanceCompleted;
use App\Domains\Transport\Events\MaintenanceScheduled;
use App\Domains\Transport\Events\RouteArchived;
use App\Domains\Transport\Events\RouteCreated;
use App\Domains\Transport\Events\RouteUpdated;
use App\Domains\Transport\Events\StopAdded;
use App\Domains\Transport\Events\StopRemoved;
use App\Domains\Transport\Events\StudentAssigned;
use App\Domains\Transport\Events\StudentUnassigned;
use App\Domains\Transport\Events\TripCancelled;
use App\Domains\Transport\Events\TripCompleted;
use App\Domains\Transport\Events\TripScheduled;
use App\Domains\Transport\Events\TripStarted;
use App\Domains\Transport\Events\VehicleRegistered;
use App\Domains\Transport\Events\VehicleUpdated;
use App\Domains\Transport\Listeners\LogDriverAssigned;
use App\Domains\Transport\Listeners\LogDriverUpdated;
use App\Domains\Transport\Listeners\LogFuelLogged;
use App\Domains\Transport\Listeners\LogMaintenanceCompleted;
use App\Domains\Transport\Listeners\LogMaintenanceScheduled;
use App\Domains\Transport\Listeners\LogRouteArchived;
use App\Domains\Transport\Listeners\LogRouteCreated;
use App\Domains\Transport\Listeners\LogRouteUpdated;
use App\Domains\Transport\Listeners\LogStopAdded;
use App\Domains\Transport\Listeners\LogStopRemoved;
use App\Domains\Transport\Listeners\LogStudentAssigned;
use App\Domains\Transport\Listeners\LogStudentUnassigned;
use App\Domains\Transport\Listeners\LogTripCancelled;
use App\Domains\Transport\Listeners\LogTripCompleted;
use App\Domains\Transport\Listeners\LogTripScheduled;
use App\Domains\Transport\Listeners\LogTripStarted;
use App\Domains\Transport\Listeners\LogVehicleRegistered;
use App\Domains\Transport\Listeners\LogVehicleUpdated;
use App\Domains\Workflow\Events\AutomationTriggered;
use App\Domains\Workflow\Events\TaskAssigned;
use App\Domains\Workflow\Events\TaskCompleted;
use App\Domains\Workflow\Events\WorkflowCompleted;
use App\Domains\Workflow\Events\WorkflowCreated;
use App\Domains\Workflow\Events\WorkflowFailed;
use App\Domains\Workflow\Events\WorkflowPublished;
use App\Domains\Workflow\Events\WorkflowStarted;
use App\Domains\Workflow\Listeners\AiIntegrationListener;
use App\Domains\Workflow\Listeners\AnalyticsCollector as WorkflowAnalyticsCollector;
use App\Domains\Workflow\Listeners\AuditLogger as WorkflowAuditLogger;
use App\Domains\Workflow\Listeners\NotificationDispatcher as WorkflowNotificationDispatcher;
use App\Domains\Workflow\Listeners\QueueDispatcher as WorkflowQueueDispatcher;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        RoleCreated::class => [],
        RoleUpdated::class => [],
        RoleAssignedToUser::class => [],
        RoleRevokedFromUser::class => [],

        PermissionCreated::class => [
            LogPermissionCreated::class,
        ],
        PermissionUpdated::class => [
            LogPermissionUpdated::class,
        ],
        PermissionArchived::class => [
            LogPermissionArchived::class,
        ],
        PermissionAssignedToRole::class => [
            LogPermissionAssignedToRole::class,
        ],
        PermissionRevokedFromRole::class => [
            LogPermissionRevokedFromRole::class,
        ],

        AcademicYearCreated::class => [
            LogAcademicYearCreated::class,
        ],
        AcademicYearUpdated::class => [
            LogAcademicYearUpdated::class,
        ],
        AcademicYearDeleted::class => [
            LogAcademicYearDeleted::class,
        ],
        AcademicYearActivated::class => [
            LogAcademicYearActivated::class,
        ],
        AcademicYearArchived::class => [
            LogAcademicYearArchived::class,
        ],
        AcademicYearCloned::class => [
            LogAcademicYearCloned::class,
        ],
        AcademicYearDatesChanged::class => [
            LogAcademicYearDatesChanged::class,
        ],
        AcademicYearSwitched::class => [
            LogAcademicYearSwitched::class,
        ],
        AcademicYearStatusChanged::class => [
            LogAcademicYearStatusChanged::class,
        ],

        AcademicTermCreated::class => [
            LogAcademicTermCreated::class,
        ],
        AcademicTermUpdated::class => [
            LogAcademicTermUpdated::class,
        ],
        AcademicTermActivated::class => [
            LogAcademicTermActivated::class,
        ],
        AcademicTermArchived::class => [
            LogAcademicTermArchived::class,
        ],
        AcademicTermStatusChanged::class => [
            LogAcademicTermStatusChanged::class,
        ],

        DepartmentCreated::class => [
            LogDepartmentCreated::class,
        ],
        DepartmentUpdated::class => [
            LogDepartmentUpdated::class,
        ],
        DepartmentArchived::class => [
            LogDepartmentArchived::class,
        ],

        ProgramCreated::class => [
            LogProgramCreated::class,
        ],
        ProgramUpdated::class => [
            LogProgramUpdated::class,
        ],
        ProgramArchived::class => [
            LogProgramArchived::class,
        ],

        GradeLevelCreated::class => [
            LogGradeLevelCreated::class,
        ],
        GradeLevelUpdated::class => [
            LogGradeLevelUpdated::class,
        ],
        GradeLevelArchived::class => [
            LogGradeLevelArchived::class,
        ],

        SectionCreated::class => [
            LogSectionCreated::class,
        ],
        SectionUpdated::class => [
            LogSectionUpdated::class,
        ],
        SectionArchived::class => [
            LogSectionArchived::class,
        ],

        AcademicClassCreated::class => [
            LogAcademicClassCreated::class,
        ],
        AcademicClassUpdated::class => [
            LogAcademicClassUpdated::class,
        ],
        AcademicClassArchived::class => [
            LogAcademicClassArchived::class,
        ],
        AcademicClassActivated::class => [
            LogAcademicClassActivated::class,
        ],

        ClassroomCreated::class => [
            LogClassroomCreated::class,
        ],
        ClassroomUpdated::class => [
            LogClassroomUpdated::class,
        ],
        ClassroomArchived::class => [
            LogClassroomArchived::class,
        ],
        ClassroomAssigned::class => [
            LogClassroomAssigned::class,
        ],
        ClassroomReleased::class => [
            LogClassroomReleased::class,
        ],

        SubjectCategoryCreated::class => [
            LogSubjectCategoryCreated::class,
        ],
        SubjectCategoryUpdated::class => [
            LogSubjectCategoryUpdated::class,
        ],
        SubjectCategoryArchived::class => [
            LogSubjectCategoryArchived::class,
        ],

        SubjectCreated::class => [
            LogSubjectCreated::class,
        ],
        SubjectUpdated::class => [
            LogSubjectUpdated::class,
        ],
        SubjectArchived::class => [
            LogSubjectArchived::class,
        ],
        SubjectActivated::class => [
            LogSubjectActivated::class,
        ],
        SubjectArchived::class => [
            LogSubjectArchived::class,
        ],
        SubjectCreated::class => [
            LogSubjectCreated::class,
        ],
        SubjectUpdated::class => [
            LogSubjectUpdated::class,
        ],

        SubjectCoefficientCreated::class => [
            LogSubjectCoefficientCreated::class,
        ],
        SubjectCoefficientUpdated::class => [
            LogSubjectCoefficientUpdated::class,
        ],
        SubjectCoefficientArchived::class => [
            LogSubjectCoefficientArchived::class,
        ],
        SubjectCoefficientActivated::class => [
            LogSubjectCoefficientActivated::class,
        ],

        GuardianCreated::class => [
            LogGuardianCreated::class,
        ],
        GuardianUpdated::class => [
            LogGuardianUpdated::class,
        ],
        GuardianArchived::class => [
            LogGuardianArchived::class,
        ],
        GuardianActivated::class => [
            LogGuardianActivated::class,
        ],

        StudentCreated::class => [
            LogStudentCreated::class,
        ],
        StudentUpdated::class => [
            LogStudentUpdated::class,
        ],
        StudentArchived::class => [
            LogStudentArchived::class,
        ],
        StudentActivated::class => [
            LogStudentActivated::class,
        ],

        StudentEnrolled::class => [
            LogStudentEnrolled::class,
        ],
        EnrollmentActivated::class => [
            LogEnrollmentActivated::class,
        ],
        EnrollmentSuspended::class => [
            LogEnrollmentSuspended::class,
        ],
        EnrollmentCompleted::class => [
            LogEnrollmentCompleted::class,
        ],
        EnrollmentWithdrawn::class => [
            LogEnrollmentWithdrawn::class,
        ],

        BookCreated::class => [
            LogBookCreated::class,
        ],
        BookBorrowed::class => [
            LogBookBorrowed::class,
        ],
        BookReturned::class => [
            LogBookReturned::class,
        ],
        ReservationCreated::class => [
            LogReservationCreated::class,
        ],
        ReservationExpired::class => [
            LogReservationExpired::class,
        ],
        FineGenerated::class => [
            LogFineGenerated::class,
        ],
        FinePaid::class => [
            LogFinePaid::class,
        ],

        VehicleRegistered::class => [
            LogVehicleRegistered::class,
        ],
        VehicleUpdated::class => [
            LogVehicleUpdated::class,
        ],
        DriverAssigned::class => [
            LogDriverAssigned::class,
        ],
        DriverUpdated::class => [
            LogDriverUpdated::class,
        ],
        RouteCreated::class => [
            LogRouteCreated::class,
        ],
        RouteUpdated::class => [
            LogRouteUpdated::class,
        ],
        RouteArchived::class => [
            LogRouteArchived::class,
        ],
        StopAdded::class => [
            LogStopAdded::class,
        ],
        StopRemoved::class => [
            LogStopRemoved::class,
        ],
        StudentAssigned::class => [
            LogStudentAssigned::class,
        ],
        StudentUnassigned::class => [
            LogStudentUnassigned::class,
        ],
        TripScheduled::class => [
            LogTripScheduled::class,
        ],
        TripStarted::class => [
            LogTripStarted::class,
        ],
        TripCompleted::class => [
            LogTripCompleted::class,
        ],
        TripCancelled::class => [
            LogTripCancelled::class,
        ],
        MaintenanceScheduled::class => [
            LogMaintenanceScheduled::class,
        ],
        MaintenanceCompleted::class => [
            LogMaintenanceCompleted::class,
        ],
        FuelLogged::class => [
            LogFuelLogged::class,
        ],

        NotificationQueued::class => [
            AuditLogger::class,
            QueueDispatcher::class,
        ],
        NotificationSent::class => [
            AuditLogger::class,
            DeliveryTracker::class,
            CacheClearer::class,
            AnalyticsIntegration::class,
        ],
        NotificationDelivered::class => [
            AuditLogger::class,
            DeliveryTracker::class,
            CacheClearer::class,
            AnalyticsIntegration::class,
        ],
        NotificationFailed::class => [
            AuditLogger::class,
            DeliveryTracker::class,
            AnalyticsIntegration::class,
        ],
        AnnouncementPublished::class => [
            AuditLogger::class,
            CacheClearer::class,
        ],
        MessageSent::class => [
            AuditLogger::class,
            QueueDispatcher::class,
        ],

        DashboardCreated::class => [
            AnalyticsAuditLogger::class,
        ],
        WidgetAdded::class => [
            AnalyticsAuditLogger::class,
        ],
        ReportGenerated::class => [
            AnalyticsAuditLogger::class,
            AnalyticsNotificationDispatcher::class,
            ExportGenerator::class,
        ],
        ReportScheduled::class => [
            AnalyticsAuditLogger::class,
            AnalyticsNotificationDispatcher::class,
        ],
        SnapshotCreated::class => [
            AnalyticsAuditLogger::class,
        ],
        KpiRecalculated::class => [
            AnalyticsAuditLogger::class,
            CacheRefresher::class,
        ],

        ApiTokenCreated::class => [
            ApiAuditLogger::class,
        ],
        ApiTokenRevoked::class => [
            ApiAuditLogger::class,
            ApiCacheClearer::class,
        ],
        WebhookRegistered::class => [
            ApiAuditLogger::class,
            ApiNotificationDispatcher::class,
        ],
        WebhookDelivered::class => [
            ApiAuditLogger::class,
        ],
        WebhookFailed::class => [
            ApiAuditLogger::class,
            RetryDispatcher::class,
        ],
        ApiClientDisabled::class => [
            ApiAuditLogger::class,
            ApiCacheClearer::class,
            ApiNotificationDispatcher::class,
        ],

        ConversationStarted::class => [
            AiAuditLogger::class,
            AiAnalyticsCollector::class,
        ],
        MessageGenerated::class => [
            AiAuditLogger::class,
            UsageTracker::class,
            AiAnalyticsCollector::class,
        ],
        ProviderChanged::class => [
            AiAuditLogger::class,
            AiCacheClearer::class,
        ],
        PromptTemplateUpdated::class => [
            AiAuditLogger::class,
            AiCacheClearer::class,
        ],
        KnowledgeIndexed::class => [
            AiAuditLogger::class,
            AiCacheClearer::class,
            AiAnalyticsCollector::class,
        ],
        UsageQuotaExceeded::class => [
            AiAuditLogger::class,
            AiNotificationDispatcher::class,
        ],

        // ── Workflow Events ──────────────────────────────────────────
        WorkflowCreated::class => [
            WorkflowAuditLogger::class,
        ],
        WorkflowPublished::class => [
            WorkflowAuditLogger::class,
            WorkflowNotificationDispatcher::class,
        ],
        WorkflowStarted::class => [
            WorkflowAuditLogger::class,
            WorkflowQueueDispatcher::class,
            WorkflowAnalyticsCollector::class,
        ],
        WorkflowCompleted::class => [
            WorkflowAuditLogger::class,
            WorkflowAnalyticsCollector::class,
            WorkflowNotificationDispatcher::class,
        ],
        WorkflowFailed::class => [
            WorkflowAuditLogger::class,
            WorkflowNotificationDispatcher::class,
        ],
        TaskAssigned::class => [
            WorkflowAuditLogger::class,
            WorkflowNotificationDispatcher::class,
        ],
        TaskCompleted::class => [
            WorkflowAuditLogger::class,
            WorkflowAnalyticsCollector::class,
        ],
        AutomationTriggered::class => [
            WorkflowAuditLogger::class,
            WorkflowAnalyticsCollector::class,
            AiIntegrationListener::class,
        ],

        // ── System Events ────────────────────────────────────────────
        SystemSettingUpdated::class => [
            SystemAuditLogger::class,
            SystemCacheRefresher::class,
        ],
        BackupCreated::class => [
            SystemAuditLogger::class,
            SystemNotificationDispatcher::class,
        ],
        BackupRestored::class => [
            SystemAuditLogger::class,
            SystemNotificationDispatcher::class,
        ],
        HealthCheckFailed::class => [
            SystemAuditLogger::class,
            HealthMonitor::class,
            SystemNotificationDispatcher::class,
        ],
        MaintenanceStarted::class => [
            SystemAuditLogger::class,
            SystemNotificationDispatcher::class,
            SystemAnalyticsCollector::class,
        ],
        MaintenanceEnded::class => [
            SystemAuditLogger::class,
            SystemNotificationDispatcher::class,
            SystemAnalyticsCollector::class,
        ],
        AuditRecorded::class => [
            SystemAnalyticsCollector::class,
        ],
    ];
}
