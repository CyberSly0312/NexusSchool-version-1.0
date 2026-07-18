<?php

namespace Database\Seeders;

use App\Domains\School\Models\School;
use App\Models\AnalyticsSnapshot;
use Illuminate\Database\Seeder;

class AnalyticsSnapshotSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::where('slug', 'nexus-demo')->first();

        if (! $school) {
            return;
        }

        $types = ['enrollment', 'attendance', 'library', 'transport', 'communication'];

        foreach ($types as $type) {
            AnalyticsSnapshot::create([
                'school_id' => $school->id,
                'snapshot_type' => $type,
                'data' => $this->getSampleData($type),
                'period_start' => now()->startOfDay(),
                'period_end' => now()->endOfDay(),
                'generated_at' => now(),
                'is_immutable' => true,
            ]);
        }
    }

    private function getSampleData(string $type): array
    {
        return match ($type) {
            'enrollment' => ['total' => 320, 'active' => 310, 'completed' => 5, 'withdrawn' => 5],
            'attendance' => ['total_records' => 1500, 'present' => 1420, 'absent' => 50, 'late' => 30],
            'library' => ['total_books' => 500, 'active_loans' => 45, 'overdue_loans' => 3, 'total_fines' => 15.00],
            'transport' => ['total_vehicles' => 8, 'active_vehicles' => 7, 'total_routes' => 5, 'total_trips_today' => 10],
            'communication' => ['total_notifications' => 200, 'unread_notifications' => 15, 'total_announcements' => 12, 'total_messages' => 50],
            default => [],
        };
    }
}
