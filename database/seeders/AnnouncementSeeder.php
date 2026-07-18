<?php

namespace Database\Seeders;

use App\Domains\School\Models\School;
use App\Models\Announcement;
use App\Models\User;
use Illuminate\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::where('slug', 'nexus-demo')->first();
        if (! $school) {
            return;
        }

        $admin = User::where('school_id', $school->id)->first();

        $announcements = [
            [
                'title' => 'New Academic Year Registration Open',
                'body' => 'Registration for the 2026/2027 academic year is now open. Please visit the admin office to complete your registration. Early bird discount available until August 30th.',
                'scope' => 'school_wide',
                'priority' => 'high',
                'status' => 'published',
                'published_at' => now(),
                'is_pinned' => true,
            ],
            [
                'title' => 'Staff Meeting - Friday',
                'body' => 'All staff members are required to attend a mandatory meeting on Friday at 2:00 PM in the conference hall.',
                'scope' => 'staff',
                'priority' => 'normal',
                'status' => 'published',
                'published_at' => now(),
            ],
            [
                'title' => 'Parent-Teacher Conference',
                'body' => 'The quarterly parent-teacher conference will be held next week. Parents are encouraged to attend and discuss their children\'s progress.',
                'scope' => 'parents',
                'priority' => 'normal',
                'status' => 'published',
                'published_at' => now(),
                'expires_at' => now()->addWeek(),
            ],
            [
                'title' => 'Sports Day Preparations',
                'body' => 'All grade levels should begin preparations for the annual sports day. Practice sessions will begin next Monday.',
                'scope' => 'school_wide',
                'priority' => 'low',
                'status' => 'draft',
            ],
        ];

        foreach ($announcements as $announcement) {
            Announcement::firstOrCreate(
                ['school_id' => $school->id, 'title' => $announcement['title']],
                array_merge($announcement, [
                    'school_id' => $school->id,
                    'author_id' => $admin?->id,
                ])
            );
        }
    }
}
