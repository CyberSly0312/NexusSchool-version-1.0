<?php

namespace Database\Seeders;

use App\Domains\School\Models\School;
use App\Models\Notification;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::where('slug', 'nexus-demo')->first();
        if (! $school) {
            return;
        }

        $notifications = [
            [
                'type' => 'system',
                'title' => 'Welcome to Nexus School ERP',
                'body' => 'Welcome to the Nexus School ERP system. Explore the dashboard to get started.',
                'status' => 'delivered',
                'channel' => 'in_app',
            ],
            [
                'type' => 'fee_reminder',
                'title' => 'Fee Payment Reminder',
                'body' => 'This is a reminder that the first term fees are due by the end of the month.',
                'status' => 'sent',
                'channel' => 'email',
            ],
            [
                'type' => 'attendance',
                'title' => 'Attendance Report Available',
                'body' => 'The weekly attendance report is now available for review.',
                'status' => 'delivered',
                'channel' => 'in_app',
            ],
            [
                'type' => 'grade',
                'title' => 'Mid-Term Results Published',
                'body' => 'Mid-term examination results have been published. Check your grades now.',
                'status' => 'pending',
                'channel' => 'in_app',
            ],
            [
                'type' => 'transport',
                'title' => 'Route Change Notification',
                'body' => 'Route A has been updated with a new stop at Market Square.',
                'status' => 'sent',
                'channel' => 'sms',
            ],
        ];

        foreach ($notifications as $notification) {
            Notification::firstOrCreate(
                ['school_id' => $school->id, 'title' => $notification['title']],
                array_merge($notification, ['school_id' => $school->id])
            );
        }
    }
}
