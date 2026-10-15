<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SchoolSeeder::class,
            PlanSeeder::class,
            RoleSeeder::class,
            PermissionSeeder::class,
            AcademicYearSeeder::class,
            AcademicTermSeeder::class,
            DepartmentSeeder::class,
            ProgramSeeder::class,
            GradeLevelSeeder::class,
            SectionSeeder::class,
            AcademicClassSeeder::class,
            ClassroomSeeder::class,
            SubjectCategorySeeder::class,
            SubjectSeeder::class,
            SubjectCoefficientSeeder::class,
            \Database\Seeders\Guardian\RelationshipTypeSeeder::class,
            \Database\Seeders\Staff\PositionSeeder::class,
            \Database\Seeders\Timetable\TeachingPeriodSeeder::class,
            GuardianSeeder::class,
            StudentSeeder::class,
            StudentEnrollmentSeeder::class,
            UserSeeder::class,
            AuthorSeeder::class,
            PublisherSeeder::class,
            BookCategorySeeder::class,
            BookSeeder::class,
            BookCopySeeder::class,
            BookLoanSeeder::class,
            ReservationSeeder::class,
            FineSeeder::class,
            VehicleSeeder::class,
            DriverSeeder::class,
            RouteSeeder::class,
            RouteStopSeeder::class,
            TripSeeder::class,
            StudentTransportAssignmentSeeder::class,
            VehicleMaintenanceSeeder::class,
            FuelLogSeeder::class,
            NotificationTemplateSeeder::class,
            NotificationSeeder::class,
            NotificationDeliverySeeder::class,
            AnnouncementSeeder::class,
            CommunicationChannelSeeder::class,
            MessageSeeder::class,
            DashboardSeeder::class,
            DashboardWidgetSeeder::class,
            ReportSeeder::class,
            KpiSeeder::class,
            AnalyticsSnapshotSeeder::class,
            ApiClientSeeder::class,
            WebhookSeeder::class,
        ]);
    }
}
