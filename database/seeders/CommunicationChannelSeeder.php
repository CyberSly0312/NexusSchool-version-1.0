<?php

namespace Database\Seeders;

use App\Domains\School\Models\School;
use App\Models\CommunicationChannel;
use Illuminate\Database\Seeder;

class CommunicationChannelSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::where('slug', 'nexus-demo')->first();
        if (! $school) {
            return;
        }

        $channels = [
            [
                'name' => 'School Email',
                'channel_type' => 'email',
                'is_active' => true,
                'from_address' => 'noreply@nexus-demo.com',
                'from_name' => 'Nexus School',
                'config' => ['driver' => 'smtp', 'host' => 'smtp.mailtrap.io', 'port' => 587],
            ],
            [
                'name' => 'SMS Gateway',
                'channel_type' => 'sms',
                'is_active' => true,
                'from_name' => 'NEXUS',
                'config' => ['provider' => 'twilio', 'from' => '+233000000000'],
            ],
            [
                'name' => 'WhatsApp Business',
                'channel_type' => 'whatsapp',
                'is_active' => false,
                'config' => ['provider' => 'whatsapp_business_api'],
            ],
            [
                'name' => 'Push Notifications',
                'channel_type' => 'push',
                'is_active' => true,
                'config' => ['driver' => 'fcm'],
            ],
            [
                'name' => 'In-App Notifications',
                'channel_type' => 'in_app',
                'is_active' => true,
                'config' => [],
            ],
        ];

        foreach ($channels as $channel) {
            CommunicationChannel::firstOrCreate(
                ['school_id' => $school->id, 'channel_type' => $channel['channel_type']],
                array_merge($channel, ['school_id' => $school->id])
            );
        }
    }
}
