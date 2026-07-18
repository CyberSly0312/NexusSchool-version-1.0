<?php

namespace Database\Seeders;

use App\Domains\School\Models\School;
use App\Models\Notification;
use App\Models\NotificationDelivery;
use Illuminate\Database\Seeder;

class NotificationDeliverySeeder extends Seeder
{
    public function run(): void
    {
        $school = School::where('slug', 'nexus-demo')->first();
        if (! $school) {
            return;
        }

        $notifications = Notification::where('school_id', $school->id)->get();

        foreach ($notifications as $notification) {
            NotificationDelivery::firstOrCreate(
                [
                    'notification_id' => $notification->id,
                    'recipient_id' => $school->id,
                ],
                [
                    'school_id' => $school->id,
                    'channel' => $notification->channel ?? 'in_app',
                    'status' => 'delivered',
                ]
            );
        }
    }
}
